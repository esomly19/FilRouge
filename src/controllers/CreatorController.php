<?php

namespace Super_Street_Dora_Grand_Championship_Turbo\controllers;

use Slim\Slim as Slim;

use Super_Street_Dora_Grand_Championship_Turbo\models\Liste as Liste;
use Super_Street_Dora_Grand_Championship_Turbo\models\Item as Item;

use Super_Street_Dora_Grand_Championship_Turbo\views\CreationListView as CLV;
use Super_Street_Dora_Grand_Championship_Turbo\views\CreationItemView as CIV;
use Super_Street_Dora_Grand_Championship_Turbo\views\SetListView as SLV;
use Super_Street_Dora_Grand_Championship_Turbo\views\SetItemView as SIV;
use Super_Street_Dora_Grand_Championship_Turbo\views\StatusView as SV;
use Super_Street_Dora_Grand_Championship_Turbo\views\DisplayAllView as DAV;
use Super_Street_Dora_Grand_Championship_Turbo\views\DisplayListView as DLV;
use Super_Street_Dora_Grand_Championship_Turbo\views\DisplayItemView as DIV;

use Super_Street_Dora_Grand_Championship_Turbo\rights\Authentication as A;
use Super_Street_Dora_Grand_Championship_Turbo\rights\IdentifyByCookies as ICB;
use Super_Street_Dora_Grand_Championship_Turbo\rights\CheckRights as CR;

use Super_Street_Dora_Grand_Championship_Turbo\exceptions\PermissionDeniedException as PDE;
use Super_Street_Dora_Grand_Championship_Turbo\exceptions\ForbiddenDataException as FDE;
use Super_Street_Dora_Grand_Championship_Turbo\exceptions\FileUploadException as FUE;

use Super_Street_Dora_Grand_Championship_Turbo\filters\Filter as Filter;

final class CreatorController implements AccessAll{
    //==================================================================================================================
    //============================================ METHODES INTERNES UTILES ============================================
    //==================================================================================================================



    /**
     *  <h3>[ METHODE INTERNE DE CLASSE - GENERER LE SCRIPT JAVA ]</h3>
     *
     * <p>Methode pour generer le code java necessaire pour copier les
     * liens vers les tokens dans le presse papier.</p>
     *
     *
     * <ul>
     *      <li>@return string $code    Code javascript pour generer le token</li>
     * </ul>
     */

    private static function getJavascriptClipboard() {
        return  "<script>
                function copyLink() {
                    //On recupere le lien.
                    var url = document.getElementById(\"link\");
                    //On le stocke dans un textarea,
                    //afin de simplifier la methode utilisee pour la copie.
                    var text = document.createElement('textarea');
                    text.value = url.href;
                    //La textarea n'est pas visible sur la page et non accessible en ecriture.
                    text.setAttribute('readonly','');
                    text.style = {position: 'absolute', left: '-10000px'
                };
                //On ajoute la tqextarea sur la page
                document.body.appendChild(text);
                //On selectionne le contenu de la textarea.
                text.select();
                //On copie le contenu dans le presse papier.
                document.execCommand(\"copy\");
                //On supprime la textarea apres la copie.
                document.body.removeChild(text);
                }               
                </script>
                <button onClick=\"copyLink()\">Copier le lien</button>";
    }

    /**
     * <h3>[ METHODE INTERNE DE CLASSE  - VERIFIER UNE URL IMAGE ]</h3>
     *
     * <p>Methode pour verifier si un lien est bien un lien qui pointe vers une image</p>
     *
     * <ul>
     *      <li><b>@param string $url</b>              URL de l'item</li>
     *      <li><b>@return bool</b>                    Vaut true si il s'agit d'un lien image./lib>
     * </ul>
     *
     *
     */
    private static function isLinkImage(string $url) : bool{
        $extensions = array("jpg", "jpeg","bmp","png");
        $extension = substr(strrchr($url,"."),1);
        if(filter_var($url, FILTER_VALIDATE_URL)) {
            if(in_array($extension, $extensions))
                return true;
        }
        return false;
    }



    //==================================================================================================================
    //============== ACCES AU LISTES ET AUX ITEMS EN TANT QUE CREATEURS AVEC LES OPTIONS DE MODIFICATION ===============
    //==================================================================================================================


    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour se rendre a la page "Mes listes" affichant les titres de toutes
     * les listes de l'utilisateur authentifie.</p>
     *
     * Un clic sur un titre de liste redirige sur la page de la liste avec ses options de modifications
     * ( autorise car l'utilisateur est authentifie et a ete identifie comme le createur de ces listes,
     * par le biais de la cle etrangere user_id dans la table liste ).
     *
     * <p>Une page affiche 5 ( constante de classe de DisplayAllView : NUMBER_LISTS_PER_PAGE )
     * listes, il suffit d'utiliser le bouton "suivant >"  pour voir les autres autres listes
     * sur une autre page, et etc, cela permet d'eviter que le chargement des listes soit trop long.</p>
     *
     * <ul>
     *      <li><b>@param int $numPage</b>      Le numero de la page a afficher.</li>
     * </ul>
     */
    public static function findAll(int $numPage){
        $lists = Liste::where('user_id','=',$_SESSION['user']);
        //Si on cherche les listes d'une page apres la page 1.
        if($numPage > 1)
            $lists = $lists
                ->skip($numPage * DAV::NUMBER_LISTS_PER_PAGE - DAV::NUMBER_LISTS_PER_PAGE)
                ->take(DAV::NUMBER_LISTS_PER_PAGE)
                ->get();
        //Sinon on cherche les listes de la page 1.
        else
            $lists = $lists
                ->take(DAV::NUMBER_LISTS_PER_PAGE)
                ->get();
        (new DAV(false, $lists, $numPage))->render();
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour se rendre a la page "Ma liste" affichant une liste.</p>
     *
     * <p>Cette page web comporte :</p>
     * <ul>
     *      <li> les details de la liste </li>
     *      <li> les informations et liens vers les items composant la liste </li>
     *      <li> un formulaire pour laisser un message public </li>
     *      <li>
     *          les options de modification de la liste
     *          ( autorise car le token est fourni )
     *      </li>
     *      <li>
     *          les 2 principales options de modification des items
     *          ( autorise car le token est fourni ) : modifier l'item
     *          ( les informations generales de l'item ), et supprimer l'item,
     *          pour avoir la totalite des options de modification des items
     *          il faut cliquer et ainsi etre redirige vers
     *          la page de l'item avec ses options de modification
     *          ( autorise car le token est fourni ).
     *      </li>
     * </ul>
     *
     * <ul>
     *      <li><b>@param int $no</b>           Le numero de la liste.</li>
     *      <li><b>@param string $token</b>     Le token de la liste.</li>
     * </ul>
     */
    public static function findMyList(int $no, string $token){
        //Si le no n'existe pas, alors on affiche un message d'erreur.
        if(!CR::isNoExisting($no))
            (new SV(SV::FAILURE_NO_ABSENT))->render();
        else{
            //Si le token n'existe pas, alors on affiche un message d'erreur.
            if(!CR::isTokenExisting($token))
                (new SV(SV::FAILURE_TOKEN_ABSENT))->render();
            else{
                //Si le token et le no ne correspondent pas a la meme liste,
                //alors on affiche un message d'erreur.
                if(!CR::isGoodTokenForList($no,$token))
                    (new SV(
                        SV::FAILURE_BAD_TOKEN_FOR_NO,
                        new PDE()
                    ))->render();
                else{
                    $list = Liste::where("no","=",$no)->first();
                    (new DLV(DLV::ACCESS_AS_CREATOR, $list, $list->messages, $list->items))->render();
                }
            }
        }
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour se rendre a la page "Mon item" affichant
     * les details portant sur un item avec les options de modification
     * ( autorise car le token est fourni ).</p>
     *
     * <p>Pour afficher un item d'une liste il faut connaitre
     * l'<b>url de la liste avec son token.</b></p>
     *
     * <p>Cette page web comporte :</p>
     * <ul>
     *      <li> les details de l'item ;</li>
     *      <li>
     *           les options de modification de l'item
     *           ( autorise car le token est fourni ).
     *      </li>
     * </ul>
     *
     * <ul>
     *      <li><b>@param int $no</b>            Le numero de la liste a laquelle appartient l'item.</li>
     *
     *      <li><b>@param int $id</b>            L'id de l'item.</li>
     *
     *      <li><b>@param string $token</b>      Le token de la liste a laquelle appartient l'item .</li>
     * </ul>
     */
    public static function findMyItem(int $no, int $id, string $token){
        //Si le no n'existe pas, alors on affiche un message d'erreur.
        if(!CR::isNoExisting($no))
            (new SV(SV::FAILURE_NO_ABSENT))->render();
        else{
            //Si l'id n'existe pas, alors on affiche un message d'erreur.
            if(!CR::isIdExisting($id))
                (new SV(SV::FAILURE_ID_ABSENT))->render();
            else{
                //Si le token n'existe pas, alors on affiche un message d'erreur.
                if(!CR::isTokenExisting($token))
                    (new SV(SV::FAILURE_TOKEN_ABSENT))->render();
                else{
                    //Si le token et le no ne correspondent pas a la meme liste,
                    //alors on affiche un message d'erreur.
                    if(!CR::isGoodTokenForList($no,$token))
                        (new SV(
                            SV::FAILURE_BAD_TOKEN_FOR_NO,
                            new PDE()
                        ))->render();
                    else{
                        //Si le token ne correspond pas a la liste a laquelle
                        //appartient l'item d'id : $id, alors on affiche un
                        //message d'erreur.
                        if(!CR::isGoodTokenForItem($id,$token))
                            (new SV(
                                SV::FAILURE_BAD_TOKEN_FOR_ID,
                                new PDE()
                            ))->render();
                        else
                            (new DIV(true,  Item::where("id","=",$id)->first()))->render();
                    }
                }
            }
        }
    }



    //==================================================================================================================
    //================================= OUVERTURE DU CREATEUR DE LISTE ET D'ITEM =======================================
    //==================================================================================================================



    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour generer la page du formulaire de creation de liste.</p>
     */
    public static function openListCreator(){
        (new CLV())->render();
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour generer la page du formulaire de creation d'item.</p>
     *
     * <ul>
     * <li><b>@param int $no</b>             Le numero de la liste dans laquelle
     *                                       on veut inserer le nouvel item. </li>
     * <li><b>@param string $token</b>       Le token de la liste dans laquelle
     *                                       on veut inserer le nouvel item. </li>
     * </ul>
     */
    public static function openItemCreator(int $no, string $token){
        //Si l'internaute a les droits pour ajouter un item a cette liste.
        if (CR::checkRightsForList($no, $token))
            (new CIV($no, $token))->render();
    }



    //==================================================================================================================
    //================================ OUVERTURE DE L'EDITEUR DE LISTE ET D'ITEM =======================================
    //==================================================================================================================


    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour generer le formulaire permettant de modifier
     * les informations generales d'une liste de souhaits.</p>
     *
     * <ul>
     *      <li><b>@param int $no</b>           Le numero de la liste.</li>
     *      <li><b>@param string $token</b>     Le token de la liste.</li>
     * </ul>
     */
    public static function openListEditor(int $no, string $token){
        //Si l'internaute a les droits pour modifier cette liste.
        if (CR::checkRightsForList($no, $token)) {
            $list = Liste::where("no", "=", $no)->first();
            (new SLV($list))->render();
        }
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour generer le formulaire permettant de modifier
     * les informations d'un item.</p>
     *
     * <ul>
     *      <li><b>@param int $id  </b>         L'item a supprimer.</li>
     *      <li><b>@param string $token</b>     Le token de la liste a laquelle appartient l'item.</li>
     * </ul>
     */
    public static function openItemEditor(int $id, string $token){
        //Si l'internaute a les droits pour modifier cet item.
        if (CR::checkRightsForItem($id, $token)) {
            $item = Item::where("id", "=", $id)->first();
            (new SIV($item, $token))->render();
        }
    }



    //==================================================================================================================
    //========== RECUPERATION DES DONNEES DES FORMULAIRES DE CREATION ET DE MODIFICATION D'ITEM ET DE LISTE ============
    //==================================================================================================================



    /**
     * [ METHODE INTERNE DE CLASSE. ]
     *
     * <p>Methode pour recuperer et filtrer (pour eviter les <b>injections</b>
     * <i>html/css</i> ou <i>sql</i>) les donnees issues d'un formulaire
     * de creation ou de modification de liste.</p>
     *
     * @param Liste $list     La liste a laquelle on insere les donnees.
     * @return bool           Vaut true si les donnnees recuperees etaient correctes.
     */
    private static function postList(Liste $list){
        try{
            //Par souci de securite, on filtre les donnees fournies, puis on insere.
            $list->titre =
                Filter::filter(Slim::getInstance()->request->post('titre'), Filter::FILTER_STRING);
            $list->description  =
                Filter::filter(Slim::getInstance()->request->post('description'), Filter::FILTER_STRING);
            $list->expiration =
                Filter::filter(Slim::getInstance()->request->post('expiration'), Filter::FILTER_DATE);
            return true;
        }catch(FDE $fde){
            $list->no == null ?
                (new SV(SV::FAILURE_CREATION_LIST, $fde))->render()
            :
                (new SV(SV::FAILURE_MODIFICATION_LIST, $fde))->render();
            return false;
        }
    }

    /**
     * [ METHODE INTERNE DE CLASSE. ]
     *
     * <p>Methode pour recuperer et filtrer (pour eviter les <b>injections</b>
     * <i>html/css</i> ou <i>sql</i>) les donnees issues d'un formulaire
     * de creation ou de modification d'item.</p>
     *
     * @param Item $item        L'item auquel on insere les donnees.
     * @return bool             Vaut true si les donnnees recuperees etaient correctes.
     */
    private static function postItem(Item $item){
        try{
            //Par souci de securite, on filtre les donnees fournies, puis on insere.
            $item->nom =
                Filter::filter(Slim::getInstance()->request->post('nom'), Filter::FILTER_STRING);
            $item->descr =
                Filter::filter(Slim::getInstance()->request->post('descr'), Filter::FILTER_STRING);
            $item->tarif =
                Filter::filter(Slim::getInstance()->request->post('tarif'), Filter::FILTER_FLOAT);

            if(Slim::getInstance()->request->post('url') != null)
                $item->url = Filter::filter(Slim::getInstance()->request->post('url'), Filter::FILTER_URL);

            return true;
        }catch(FDE $fde){
            $item->id == null ?
                (new SV(SV::FAILURE_CREATION_ITEM, $fde))->render()
            :
                (new SV(SV::FAILURE_MODIFICATION_ITEM, $fde))->render();
            return false;
        }
    }



    //==================================================================================================================
    //================================== AJOUT DE LISTE, D'ITEM ET DE CAGNOTTE =========================================
    //==================================================================================================================



    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode ajouter une liste. On traite les donnees fournies dans le formulaire de creation de liste.
     * Note : un participant devient un createur a la creation de sa premiere liste. N'importe qui
     * peur creer une liste par ailleurs. Si le createur de la liste n'est pas authentifie, alors
     * on lui fournit <b>l'url avec le token</b> pour acceder a sa liste. Un createur qui etait authentifie
     * avant la creation de sa liste peut acceder a sa liste par le biais de la page <b>"Mes listes"</b>.</p>
     *
     * <p>Les donnees sont recuperees, filtrees (pour eviter les <b>injections</b> <i>html/css</i> ou <i>sql</i>),
     * et enfin inserees dans la table (par le biais de la methode <i>save()</i> du patron <i>ActiveRecord</i> de
     * <i>Eloquent</i>).</p>
     */
    public static function addList(){
        $list = new Liste;

        //On filtre et on insere les valeurs issues du $_POST dans
        //les attributs de la liste.
        //Si la fonction retourne false alors une des donnees etait
        //incorrecte, on arrete le processus d'ajout de liste.
        if(!self::postList($list))
            //On oblige l'execution de la methode a se terminer ici.
            return;

        try{
            //Le token ne doit pas pouvoir etre devine par les autres participants et autres utilisateurs.
            //C'est pourquoi on cree un token avec une fonction qui genere une chaine aleatoire
            $list->token = bin2hex(random_bytes(8));
        } catch (\Exception $e) {
            (new SV(SV::FAILURE_CREATION_LIST, $e))->render();
            //On oblige l'execution de la methode a se terminer ici.
            return;
        }

        /**
         * [ GESTION DE LA SESSION. ]
         *
         * Si un utilisateur est connecte alors l'attribut
         * $list->user_id est defini avec son no.
         */
        A::saveOwner($list);

        //Appel a la methode save() du patron ActiveRecord de Eloquent.
        //Insertion du tuple dans la table.
        $list->save();
        
        /**
         * [ GESTION DES COOKIES. ]
         *
         *  Creation d'un cookie temoin des droits du client
         *  sur cette nouvelle liste.
         */
        ICB::saveOwner($list);

        if(A::isConnected())
            (new SV(SV::SUCCESS_CREATION_LIST))->render();
        else{
            /* POUR UN SERVEUR LOCAL AVEC UN PORT PERSONNALISE :
                $linkContent =
                Slim::getInstance()->request->getHostWithPort() .
                Slim::getInstance()->urlFor('VIEW_CREATOR_LIST', ['no' => $list->no, 'token' => $list->token]);
            */

            /* POUR WEBETU */
            $linkContent =
                Slim::getInstance()->request->getHost().
                Slim::getInstance()->urlFor('VIEW_CREATOR_LIST', ['no' => $list->no, 'token' => $list->token]);

            $link = "<a id='link' href='http://".$linkContent."'>http://".$linkContent."</a>";

            $message = "
                <br>
                ZUT ! Vous n'etes pas connecte a un compte d'utilisateur !
                <br>
                Par consequent, vous n'avez qu'un seul moyen pour acceder a la liste,
                que vous venez de creer, vous devrez utiliser le token :  
                $list->token.
                <br>
                Utilisez cet url contenant le token de votre liste : $link 
                <br>" .
                self::getJavascriptClipboard();
            (new SV(SV::SUCCESS_CREATION_LIST, $message))->render();
        }
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour ajouter un item a une liste.</p>
     *
     * <p>Les donnees sont recuperees, filtrees (pour eviter les <b>injections</b> <i>html/css</i> ou <i>sql</i>),
     * et enfin inserees dans la table (par le biais de la methode <i>save()</i> du patron <i>ActiveRecord</i>
     * de <i>Eloquent</i>).</p>
     *
     * <ul>
     *      <li>@param int $no           Le numero de la liste.</li>
     *      <li>@param string $token     Le token de la liste.</li>
     * </ul>
     */
    public static function addItem(int $no, string $token){
        //Si l'internaute a les droits pour ajouter un item a cette liste.
        if (CR::checkRightsForList($no, $token)) {
            $item = new Item;

            $item->liste_id = $no;

            //On filtre et on insere les valeurs issues du $_POST dans
            //les attributs de l'item.
            //Si la fonction retourne false alors une des donnees etait
            //incorrecte, on arrete le processus d'ajout d'item.
            if(!self::postItem($item))
                //On oblige l'execution de la methode a se terminer ici.
                return;

            //Appel a la methode save() du patron ActiveRecord de Eloquent.
            //Insertion du tuple dans la table.
            $item->save();

            (new SV(SV::SUCCESS_CREATION_ITEM))->render();
        }
    }



    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour ajouter une cagnotte a un item.</p>
     *
     * <ul>
     *      <li>@param int $id          L'id de l'item.</li>
     *      <li>@param string $token     Le token de la liste.</li>
     * </ul>
     */
    public static function addPot(int $id, string $token){
        //Si l'internaute a les droits pour ajouter une cagnote.
        if(CR::checkRightsForItem($id, $token)){
            $item = Item::where('id','=',$id)->first();
            $item->cagnotte = true;
            //Appel a la methode save() du patron ActiveRecord de Eloquent.
            //Mise a jour du tuple dans la table.
            $item->save();
            (new SV(SV::SUCCESS_CREATION_POT))->render();
        }
    }



    //==================================================================================================================
    //==================================== MODIFICATION D'ITEM ET DE LISTE =============================================
    //==================================================================================================================



    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour modifier une liste.</p>
     *
     * <p>Les donnees sont recuperees, filtrees (pour eviter les <b>injections</b> <i>html/css</i> ou <i>sql</i>),
     * et enfin le tuple est mis a jour dans la table (par le biais de la methode <i>save()</i> du patron
     * <i>ActiveRecord</i> de <i>Eloquent</i>).</p>
     *
     * <ul>
     *      <li><b>@param int $no</b>           Le numero de la liste.</li>
     *      <li><b>@param string $token</b>     Le token de la liste.</li>
     * </ul>
     */
    public static function setList(int $no, string $token){
        //Si l'internaute a les droits pour modifier la liste.
        if (CR::checkRightsForList($no, $token)) {
            $list = Liste::where('no', '=', $no)->first();

            //On filtre et on insere les valeurs issues du $_POST dans
            //les attributs de la liste.
            //Si la foncion retourne false alors une des donnees etait
            //incorrecte, on arrete le processus de modification de liste.
            if(!self::postList($list))
                //On oblige l'execution de la methode a se terminer ici.
                return;

            //Appel a la methode save() du patron ActiveRecord de Eloquent.
            //Mise a jour du tuple dans la table.
            $list->save();
            (new SV(SV::SUCCESS_MODIFICATION_LIST))->render();
        }
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour modifier un item d'une liste.</p>
     *
     * <p>Les donnees sont recuperees, filtrees (pour eviter les <b>injections</b> <i>html/css</i> ou <i>sql</i>),
     * et enfin le tuple est mis a jour dans la table (par le biais de la methode <i>save()</i> du patron
     * <i>ActiveRecord</i> de <i>Eloquent</i>).</p>
     *
     * <ul>
     *      <li><b>@param int $id</b>           L'id de l'item.</li>
     *      <li><b>@param string $token</b>     Le token de la liste a laquelle appertient l'item.</li>
     * </ul>
     */
    public static function setItem(int $id, string $token){
        $item = Item::where('id', '=', $id)->first();
        //Si l'internaute a les droits pour modifier cet item et que l'item n'est pas reserve.
        if (CR::checkRightsForItem($id, $token) and !CR::isReserved($item)) {

            //On filtre et on insere les valeurs issues du $_POST dans
            //les attributs de l'item.
            //Si la foncion retourne false alors une des donnees etait
            //incorrecte, on arrete le processus de modification d'item.
            if(!self::postItem($item))
                //On oblige l'execution de la methode a se terminer ici.
                return;

            //Appel a la methode save() du patron ActiveRecord de Eloquent.
            //Mise a jour du tuple dans la table.
            $item->save();
            (new SV(SV::SUCCESS_MODIFICATION_ITEM))->render();
        }
    }



    //==================================================================================================================
    //==== AJOUT ET MODIFICATION D'IMAGE PAR UPLOAD, HOT-LINKING, OU EN PRECISANT LE NOM DE L'IMAGE DANS LE WEB/IMG ====
    //==================================================================================================================



    /**
     * <h3>[ METHODE DE CLASSE - AJOUTER/MODIFIER L'IMAGE D'UN ITEM  - PAR HOT-LINKING OU WEB/IMG/. ]</h3>
     *
     * <p>Methode pour ajouter/modifier une image a un item d'une liste par le hot-linking
     * ou en utilisant une image deja existante dans le dossier web/img/.</p>
     *
     * <p>Les donnees sont recuperees, filtrees (pour eviter les <b>injections</b> <i>html/css</i> ou <i>sql</i>),
     * et enfin inserees dans la table (par le biais de la methode <i>save()</i> du patron <i>ActiveRecord</i>
     * de <i>Eloquent</i>).</p>
     *
     * <ul>
     *      <li><b>@param int $id</b>           L'id de l'item.</li>
     *      <li><b>@param string $token</b>     Le token de la liste a laquelle appertient l'item.</li>
     * </ul>
     * @throws FDE
     */
    public static function setItemImage(int $id, string $token){
        $item = Item::where('id', '=', $id)->first();
        //Si l'internaute a les droits pour ajouter/modifeor une image a cet item.
        if (CR::checkRightsForItem($id, $token) and !CR::isReserved($item)) {
            //Par souci de securite, on filtre les donnees fournies, puis on insere.
            try{

                $imageLink = Filter::filter(Slim::getInstance()->request->post('img'), Filter::FILTER_STRING);
                $item->img = $imageLink;
                if(CreatorController::isLinkImage($imageLink))
                    $item->hotLinking = true;
                else{
                    if(file_exists("img/".$imageLink))
                        $item->hotLinking = false;
                    else{
                        (new SV(
                            SV::FAILURE_MODIFICATION_IMAGE_ITEM,
                            SV::IMAGE_ABSENT)
                        )->render();
                        //On oblige l'execution de la methode a se terminer ici.
                        return;
                    }
                }
            }catch(FDE $fde){
                (new SV(SV::FAILURE_MODIFICATION_IMAGE_ITEM, $fde))->render();
                //On oblige l'execution de la methode a se terminer ici.
                return;
            }

            //Appel a la methode save() du patron ActiveRecord de Eloquent.
            //Insertion ou mise a jour du tuple (selon si c'est un ajout ou une modification de l'image)
            //dans la table.
            $item->save();
            (new SV(SV::SUCCESS_MODIFICATION_IMAGE_ITEM))->render();
        }
    }

    /**
     * <h3>[ METHODE DE CLASSE - AJOUTER/MODIFIER L'IMAGE D'UN ITEM - PAR UPLOAD ]</h3>
     *
     * <p>Methode pour modifier/ajouter une image a un item d'une liste en uplodant l'image.</p>
     *
     * <p>Les donnees sont recuperees, filtrees (pour eviter les <b>injections</b> <i>html/css</i> ou <i>sql</i>),
     * et enfin inserees dans la table (par le biais de la methode <i>save()</i> du patron <i>ActiveRecord</i> de
     * <i>Eloquent</i>).</p>
     *
     * <ul>
     *      <li><b>@param int $id</b>           L'id de l'item.</li>
     *      <li><b>@param string $token</b>     Le token de la liste a laquelle appertient l'item.</li>
     * </ul>
     */
    public static function uploadItemImage(int $id, string $token){
        $item = Item::where('id', '=', $id)->first();
        //Si l'internaute a les droits pour ajout une image a cet item.
        if(CR::checkRightsForItem($id, $token) and !CR::isReserved($item)){
            //On recupere le nom de l'image uploadee.
            $imageName = basename($_FILES['img']['name']);
            if(str_contains($imageName," ")) {
                (new SV(
                    SV::FAILURE_MODIFICATION_IMAGE_ITEM,
                    FUE::FILE_CONTAINS_SPACES
                ))->render();
            }
            //On recupere le chemin d'acces local vers le dossier des images,
            //selon le serveur auquel on ajoute le nom de l'image.
            $fileImage = 'img/' . $imageName;

            //Si le type de fichier n'est pas tolere,
            //on genere un message d'erreur :
            if($_FILES['img']['type'] != 'image/jpeg' and $_FILES['img']['type'] !=  'image/png'
                and $_FILES['img']['type'] != 'image/bmp')
                (new SV(
                    SV::FAILURE_MODIFICATION_IMAGE_ITEM,
                    FUE::INCORRECT_FILE_FORMAT
                ))->render();
            else{
                //Si le fichier est trop gros ( taille maxi 15 mb),
                //on genere un message d'erreur.
                if ($_FILES['img']['size'] > 15728640)
                    (new SV(
                        SV::FAILURE_MODIFICATION_IMAGE_ITEM,
                        FUE::FILE_TOO_BIG
                    ))->render();
                else{
                    //Si un fichier dans web/img/ porte deja ce nom,
                    //on genere un mesage d'erreur.
                    if (file_exists($fileImage))
                        (new SV(
                            SV::FAILURE_MODIFICATION_IMAGE_ITEM,
                            FUE::COPY_NAME_ERROR
                        ))->render();
                    else{
                        //Si on arrive pas a deplacer le fichier uploade
                        //dans le repertoire web/img/,
                        //on genere un message d'erreur.
                        if (!move_uploaded_file($_FILES['img']['tmp_name'], $fileImage))
                            (new SV(
                                SV::FAILURE_MODIFICATION_IMAGE_ITEM,
                                FUE::COPY_ERROR
                            ))->render();
                        else{
                            //On associe le fichier a l'image dans la base de donnees et par
                            //souci de securite, on filtre les donnees fournies, puis on insere.
                            try{
                                $item->img = Filter::filter($imageName, Filter::FILTER_STRING);
                                // L'image a ete envoye par upload, hotLinking = 1
                                $item->hotLinking = false;
                            }catch (FDE $fde){
                                (new SV(SV::FAILURE_MODIFICATION_IMAGE_ITEM, $fde))->render();
                                //On oblige l'execution de la methode a se terminer ici.
                                return;
                            }
                            //Appel a la methode save() du patron ActiveRecord de Eloquent.
                            //Insertion ou mise a jour du tuple (selon si c'est un ajout ou une modification de l'image)
                            //dans la table.
                            $item->save();
                            (new SV(SV::SUCCESS_MODIFICATION_IMAGE_ITEM))->render();
                        }
                    }
                }
            }
        }
    }



    //==================================================================================================================
    //======================================= SUPPRESSION DES ITEMS ET DES IMAGES ======================================
    //==================================================================================================================




    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour supprimer un item d'une liste.</p>
     *
     * <ul>
     *      <li><b>@param int $id</b>           L'item a supprimer.</li>
     *      <li><b>@param string $token</b>     Le token de la liste a laquelle appartient l'item.</li>
     * </ul>
     */
    public static function deleteItem(int $id, string $token){
        $item = Item::where('id', '=', $id)->first();
        //Si l'internaute a les droits pour supprimer cet item  cet item et que l'item n'est pas reserve.
        if (CR::checkRightsForItem($id, $token) and !CR::isReserved($item)) {
            //Appel a la methode delete() du patron ActiveRecord de Eloquent.
            //Suppression du tuple dans la table.
            $item->delete();
            (new SV(SV::SUCCESS_DELETION_ITEM))->render();
        }
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour supprimer l'image d'un item d'une liste.</p>
     *
     * <ul>
     *      <li><b>@param int $id</b>           L'item a supprimer.</li>
     *      <li><b>@param string $token</b>     Le token de la liste a laquelle appartient l'item.</li>
     * </ul>
     */
    public static function deleteItemImage(int $id, string $token){
        $item = Item::where('id', '=', $id)->first();
        //Si l'internaute a les droits pour supprimer l'image de cet item et que l'item n'est pas reserve.
        if (CR::checkRightsForItem($id, $token) and !CR::isReserved($item)) {
            $item->img = null;
            //Appel a la methode save() du patron ActiveRecord de Eloquent.
            //Mise a jour du tuple dans la table.
            $item->save();
            (new SV(SV::SUCCESS_DELETION_IMAGE_ITEM))->render();
        }
    }




    //==================================================================================================================
    //============================================= PARTAGE DES LISTES =================================================
    //==================================================================================================================



    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour partager une liste. Note : une liste deja partagee
     * ne peut etre re-partagee, ca n'aurait aucun interet.</p>
     *
     * <ul>
     *      <li><b>@param int $no</b>           Le numero de la liste a partager.</li>
     *      <li><b>@param string $token</b>     Le token de modification de la liste a partager.</li>
     * </ul>
     */
    public static function share(int $no, string $token){
        //Si l'internaute a les droits pour publier cette liste.
        if (CR::checkRightsForList($no, $token)) {
            //Si la liste est partageable
            //( = pas deja partagee ).
            if(!CR::isShared($no)){
                $list = Liste::where('no', '=', $no)->first();

                //On genere le token de partage.
                try {
                    //Le token ne doit pas pouvoir etre deviner par les autres participants et autres utilisateurs.
                    //Ce pourquoi on creer un token avec une fonction qui genere de maniere aleatoire une
                    // chaine aleatoire.
                    $list->tokenPartage = bin2hex(random_bytes(8));
                }catch (\Exception $e){
                    (new SV(SV::FAILURE_SHARING_LIST, $e))->render();
                    //On oblige l'execution de la methode a se terminer ici.
                    return;
                }

                //Appel a la methode save() du patron ActiveRecord de Eloquent.
                //Mise a jour du tuple dans la table.
                $list->save();

                /* POUR UN SERVEUR LOCAL AVEC UN PORT PERSONNALISE :
              $linkContent =
              Slim::getInstance()->request->getHostWithPort() .
              Slim::getInstance()->urlFor('VIEW_CREATOR_LIST', ['no' => $list->no, 'token' => $list->token]);
                */

                /* POUR WEBETU */
                $linkContent =
                    Slim::getInstance()->request->getHost().
                    Slim::getInstance()->urlFor('VIEW_CREATOR_LIST', ['no' => $list->no, 'token' => $list->token]);
                $link = "<a id='link' href='http://" . $linkContent . "'>http://" . $linkContent . "</a>";

                (new SV(
                    SV::SUCCESS_SHARING_LIST,
                    "Le lien de partage de votre liste est : <br>" . $link . self::getJavascriptClipboard()
                ))->render();
            }else
                (new SV(
                    SV::FAILURE_SHARING_LIST,
                    SV::LIST_ALREADY_SHARED
                ))->render();
        }
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour publier une liste. Note : une liste deja publiee
     * ne peut etre re-publiee, ca n'aurait aucun interet.</p>
     *
     * <ul>
     *      <li><b>@param int $no</b>           Le numero de la liste a publier.</li>
     *      <li><b>@param string $token</b>     Le token de la liste a publier.</li>
     * </ul>
     */
    public static function publish(int $no, string $token){
        //Si l'internaute a les droits pour publier cette liste.
        if (CR::checkRightsForList($no, $token)) {
            //Si la liste est publiable ( = pas deja publiee ).
            if(!CR::isPublic($no)){
                $list = Liste::where('no', '=', $no)->first();
                $list->publique = true;
                //Appel a la methode save() du patron ActiveRecord de Eloquent.
                //Mise a jour du tuple dans la table.
                $list->save();
                (new SV(SV::SUCCESS_PUBLICATION_LIST))->render();
            }else
                (new SV(
                    SV::FAILURE_PUBLICATION_LIST,
                    SV::LIST_ALREADY_PUBLIC
                ))->render();
        }
    }



    //==================================================================================================================
    //==================================================================================================================
}