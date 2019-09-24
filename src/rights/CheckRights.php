<?php
/**
 *  <h2>[ CLASSE POUR LE CONTROLE DES DROITS. ]</h2>
 *
 *  <p>Cette classe sert a verifier le droit d'un internaute a apporter
 *  des modifications a une liste ou a un item.</p>
 *
 *  <p>Elle gere les problemes de ressources demandees inexistantes,
 *  ainsi que les problemes d'incoherence entre le token fourni et la ressource demandee.</p>
 *
 *  <p>Les methodes : <i>checkRightsForList</i>(int $no, string $token) : bool et
 *  checkRightsForItem(int $id, string $token) permettent de factoriser de maniere consequente le
 *  code de l'application web, elles sont souvent utilisees dans les controleurs.</p>
 *
 *  <p>Ceci est une classe concrete "<b>sterile</b>", c-a-d qu'aucune classe ne peut
 *  en heriter ( d'ou la presence du final devant class).</p>
 *
 * @author PALMIERI Adrien, VANCOILLE Victor, IANCEK Arthur, CHEVRIER Jean-Christophe.
 */
namespace mywishlist\rights;

use mywishlist\models\Liste as Liste;
use mywishlist\models\Item as Item;

use mywishlist\views\StatusView as SV;

use mywishlist\exceptions\PermissionDeniedException as PDE;

final class CheckRights
{
    //==================================================================================================================
    //================================ VERIFIER DES INFORMATIONS DONNEES DANS L'URL ====================================
    //==================================================================================================================

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour verifier un numero d'une liste.</p>
     *
     * <ul>
     *      <li><b>@param string $no</b>        Le numero d'une liste a verifier.</li><br/>
     * <br>
     *      <li><b>@return bool</b>             Vaut true si une liste existe pour ce numero.</li>
     * </ul>
     */
    public static function isNoExisting(string $no) : bool {
        return (Liste::where('no','=',$no)
                      ->count()
                === 1);
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour verifier un id d'un item.</p>
     *
     * <ul>
     *      <li><b>@param int $id</b>           L'id d'un item a verifier.</li>
     * <br>
     *      <li><b>@return bool</b>             Vaut true si un item exite pour cet id.</li>
     * </ul>
     */
    public static function isIdExisting(int $id) : bool {
        return (Item::where('id','=',$id)
                    ->count()
                === 1);
    }


    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour verifier un token d'une liste.</p>
     *
     * <ul>
     *      <li><b>@param string $token</b>     Le token d'une liste a verifier.</li><br/>
     * <br>
     *      <li><b>@return bool</b>             Vaut true si au moins une liste possede ce token.</li>
     * </ul>
     */
    public static function isTokenExisting(string $token) : bool {
        //on utilise >= 1 car plusieurs listes peuvent avoir le meme token
        //(car les tokens sont generes de maniere aleatoire
        //et il existe une infime probabilite qu'un meme token soit
        //genere plusieurs fois) et on cherche juste a verifier que
        //le token existe.
        return (Liste::where('token','=',$token)
                ->count()
                 >= 1);
    }


    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour verifier un token de partage d'une liste.</p>
     *
     * <ul>
     *      <li><b>@param string $shareToken/b>     Le token de partage d'une liste a verifier.</li><br/>
     * <br>
     *      <li><b>@return bool</b>                 Vaut true si au moins une liste possede ce token de partage.</li>
     * </ul>
     */
    public static function isShareTokenExisting(string $shareToken) : bool {
        //on utilise >= 1 car plusieurs listes peuvent avoir le meme token
        //de partage (car les tokens sont generes de maniere aleatoire
        //et il existe une infime probabilite qu'un meme token soit
        //genere plusieurs fois) et on cherche juste a verifier que
        //le token existe.
        return (Liste::where('tokenPartage','=',$shareToken)
                ->count()
                >= 1);
    }



    //==================================================================================================================
    //============================== VERIFIER LA CORRESPONDANCE ENTRE LES INFORMATIONS =================================
    //==================================================================================================================



    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour verifier que le no correspond a la liste a laquelle
     * appartient l'item.</p>
     *
     * <ul>
     *      <li><b>@param int $no</b>           Le no de la liste a laquelle appartient l'item.</li>
     *      <li><b>@param int $id</b>           L'id de l'item.</li>
     * <br>
     *      <li><b>@return bool</b>             Vaut true si le no correspond a la liste a laquelle
     *                                          appartient l'item.</li>
     * </ul>
     */
    public static function isGoodIdForNo(int $no, int $id) : bool {
        return (Item::where('id','=',$id)
                ->first()->liste_id
                 ===
                 $no);
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour verifier la correspondance entre un token
     * et un numero.</p>
     *
     * <ul>
     *      <li><b>@param int $no</b>           Le numero de la liste.</li>
     *      <li><b>@param string $token</b>     Le token de la liste.</li>
     * <br>
     *      <li><b>@return bool</b>             Vaut true si le token et le numero correspondent
     *                                          a la meme liste.</li>
     * </ul>
     */
    public static function isGoodTokenForList(int $no, string $token) : bool {
        return (Liste::where('no','=',$no)
                    ->where('token','=',$token)
                    ->count()
                === 1);
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour verifier que le token correspond a la liste a laquelle
     * appartient l'item.</p>
     *
     * <ul>
     *      <li><b>@param int $id</b>           L'id de l'item.</li>
     *      <li><b>@param string $token</b>     Le token de la liste.</li>
     * <br>
     *      <li><b>@return bool</b>             Vaut true si le token correspond a la liste a laquelle
     *                                          appartient l'item.</li>
     * </ul>
     */
    public static function isGoodTokenForItem(int $id, string $token) : bool {
        return (Liste::where
                    (   'no',
                        '=',
                        Item::where('id','=',$id)
                        ->first()
                        ->liste
                        ->no
                    )
                    ->where('token','=',$token)
                    ->count()
                 === 1);
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour verifier si il y a uune correspondance entre
     * le token de partage et le no.</p>
     *
     * <ul>
     *      <li><b>@param int $no</b>           Le numero de la liste.</li>
     *      <li><b>@param string $shareToken</b>   Le token de partage de la liste</li>
     * <br>
     *      <li><b>@return bool</b>             Vaut true si la liste est partageable.</li>
     * </ul>
     */
    public static function isGoodShareTokenForNo(int $no, string $shareToken) : bool {
        return (Liste::where('no','=',$no)
                ->where('tokenPartage','=',$shareToken)
                ->count()
            === 1);
    }



    //==================================================================================================================
    //============================ SAVOIR SI UNE LISTE EST PUBLIQUE ET/OU PARTAGEE =====================================
    //==================================================================================================================



    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour verifier si une liste est partagee. Une liste
     * est partagee si son attribut tokenPartage n'est pas null.</p>
     *
     * <ul>
     *      <li><b>@param int $no</b>           Le numero de la liste.</li>
     * <br>
     *      <li><b>@return bool</b>             Vaut true si la liste est partagee.</li>
     * </ul>
     */
    public static function isShared(int $no) : bool {
        return(Liste::where('no','=',$no)
                ->first()
                ->tokenPartage != null);
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour verifier si une liste est publique.</p>
     *
     * <ul>
     *      <li><b>@param int $no</b>           Le numero de la liste.</li>
     * <br>
     *      <li><b>@return bool</b>             Vaut true si la liste est publique.</li>
     * </ul>
     */
    public static function isPublic(int $no) : bool {
        return(Liste::where('no','=',$no)
                ->first()
                ->publique == true);
    }



    //==================================================================================================================
    //======================================= SAVOIR SI UN ITEM EST RESERVE ============================================
    //==================================================================================================================


    /**
     * [ METHODE DE CLASSE. ]
     *
     * <p>Methode pour savoir si un item est reserve.</p>
     *
     * <ul>
     *      <li><b>@param Item $item</b>        L'item en question</li>
     * <br>
     *      <li><b>@return bool</b>             Vaut true si l'item est reserve.</li>
     * </ul>
     */
    public static function isReserved(Item $item) : bool {
        return $item->reservation == true;
    }



    //==================================================================================================================
    // VERIFICATION DES INFORMATIONS ET DE LEUR CORRESPONDANCE ET BLOQUAGE DE LA RESSOURCE ET REDIRECTION SI NECESSAIRE
    //==================================================================================================================



    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour le controle des droits de modification d'une liste
     * par un internaute.</p>
     *
     * <p>La methode verifie d'abord que la liste de numero : $no existe,
     * et de meme pour le token : $token. Puis ensuite elle verifie que le token fourni est
     * bien celui de la liste.</p>
     *
     * <p>Si $no et/ou $token n'existent pas, un message d'erreur est affiche par le biais de
     * la vue mywishlist\views\StatusView.</p>
     *
     * <p>Si $no et $token ne correspondent pas a la meme liste, un message d'erreur est egalement
     * affiche par le biais de la vue mywishlist\views\StatusView. Et une exception de type
     * mywishlist\exceptions\PermissionDeniedException est utilisee pour expliquer a l'internaute que
     * les permissions d'acces a la ressource demandee lui ont ete refuses.</p>
     *
     * <p>Dans un souci de reduction de la taille du code, nous avons choisi
     * d'utiliser des instances anonymes de StatusView.</p>
     *
     * <ul>
     *      <li><b>@param int $no</b>           Le numero de la liste.</li>
     *      <li><b>@param string $token</b>     Le token de la liste.</li>
     * <br>
     *      <li><b>@return bool</b>             Vaut true si permissions d'acces accordees.</li>
     * <ul>
     */
    public static function checkRightsForList(int $no, string $token) : bool {
        //Si le no existe.
        if(self::isNoExisting($no)){
            //Si le token existe.
            if(self::isTokenExisting($token)){
                //Si le le token et le no correspondent a la meme liste.
                if(self::isGoodTokenForList($no,$token))
                    return true;
                //Sinon on affiche un message d'erreur.
                else
                    (new SV(
                        SV::FAILURE_BAD_TOKEN_FOR_NO,
                        new PDE()
                    ))->render();
                //Sinon on affiche un message d'erreur.
            }else
                (new SV(SV::FAILURE_TOKEN_ABSENT))->render();
            //Sinon on affiche un message d'erreur.
        }else
            (new SV(SV::FAILURE_NO_ABSENT))->render();

        return false;
    }


    /**
     * <h2>[ METHODE DE CLASSE. ]</h2>
     *
     * <p>Methode pour le controle des droits de modification d'une liste
     * par un internaute.</p>
     *
     * <p>La methode verifie d'abord que l'item d'id : $id existe,
     * et de meme pour le token : $token. Puis ensuite elle verifie que le token fourni est
     * bien celui de la liste a laquelle appartient l'item.</p>
     *
     * <p>Si $id et/ou $token n'existent pas, un message d'erreur est affiche par le biais de
     * la vue mywishlist\views\StatusView.</p>
     *
     * <p>Si $token ne correspond pas a la liste de l'item, un message d'erreur est egalement
     * affiche par le biais de la vue mywishlist\views\StatusView.Et une exception de type
     * mywishlist\exceptions\PermissionDeniedException est utilisee pour expliquer a l'internaute que
     * les permissions d'acces a la ressource demandee lui ont ete refuses.</p>
     *
     * <p>Dans un souci de reduction de la taille du code, nous avons choisi
     * d'utiliser des instances anonymes de StatusView.</p>
     *
     * <ul>
     *      <li><b>@param int $id</b>           L'id de l'item.</li>
     *      <li><b>@param string $token</b>     Le token de la liste.</li></br>
     * <br>
     *      <li><b>@return bool</b>             Vaut true si permissions d'acces accordees.</li>
     * </ul>
     */
    public static function checkRightsForItem(int $id, string $token) : bool {
        //Si l'id existe.
        if(self::isIdExisting($id)){
            //Si le token existe.
            if(self::isTokenExisting($token)){
                //Si le token correspond a liste a laquelle appartient l'id.
                if(self::isGoodTokenForItem($id,$token))
                    return true;
                //Sinon on affiche un message d'erreur.
                else
                    (new SV(
                        SV::FAILURE_BAD_TOKEN_FOR_ID,
                        new PDE()
                    ))->render();
                //Sinon on affiche un message d'erreur.
            }else
                (new SV(SV::FAILURE_TOKEN_ABSENT))->render();

            //Sinon on affiche un message d'erreur.
        }else
            (new SV(SV::FAILURE_ID_ABSENT))->render();

        return false;
    }


    //==================================================================================================================
    //==================================================================================================================
}