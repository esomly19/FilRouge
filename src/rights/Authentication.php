<?php
/**
 * <h2>[ CLASSE POUR L'AUTHENTIFICATION. ]</h2>
 *
 * <ul>
 * Cette classe permet de gerer :
 *      <li> la creation d'utilisateur ;</li>
 *      <li> la suppression d'utilisateur ;</li>
 *      <li> la connexion sur le site (verification des identifiants de connexion);</li>
 *      <li> la deconnexion ;</li>
 *      <li> la mise en place d'un temoin de connexion (variable de session) ;</li>
 *      <li> le respect des niveaux de permissions d'acces ;</li>
 *      <li> savoir si un utilisateur est connecte ;</li>
 *      <li> sauver le lien entre une liste nouvellement creee et son createur : l'utilisateur connecte ;</li>
 *      <li> savoir si une liste appartient a l'utilisateur connecte.</li>
 * </ul>
 *
 * <p><b>Note</b> : pour la gestion des sessions, nous avons considere que les utiliser
 * sans les methodes proposees par Slim etait plus simple.</p>
 *
 * <p>Ceci est une classe concrete "sterile", c-a-d qu'aucune classe ne peut
 * en heriter ( d'ou la presence du final devant class).</p>
 *
 * @author PALMIERI Adrien, VANCOILLE Victor, IANCEK Arthur, CHEVRIER Jean-Christophe.
 */
namespace mywishlist\rights;

use mywishlist\models\Role as Role;
use mywishlist\models\Compte as Compte;
use mywishlist\models\Liste as Liste;
use mywishlist\models\Message as Message;
use mywishlist\models\Item as Item;

use mywishlist\exceptions\BadPasswordException as BPE;
use mywishlist\exceptions\UsernameException as UE;
use mywishlist\exceptions\AuthenticationFailedException as AFE;

final class Authentication implements Owner{
    //==================================================================================================================
    //==================================================================================================================



    /**
     * [ CONSTANTE DE CLASSE. ]
     *
     * Type de permissions d'acces.
     */
    const PARTICIPANT_RIGHTS_LEVEL = 0;
    const CREATOR_RIGHTS_LEVEL = 1;



    //==================================================================================================================
    //============================== UN UTILISATEUR EST-IL CONNECTE EN CE MOMENT ? =====================================
    //==================================================================================================================



    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode indiquant si un utilisateur est connecte sur le site.</p>
     *
     * <ul>
     *      <li><ul>@return bool</ul>     Vaut true si un untilisateur est connecte.</li>
     * </ul>
     */
    public static function isConnected() : bool {
        return isset($_SESSION['user']);
    }



    //==================================================================================================================
    //========================================= VERIFICATION DU MOT DE PASSE ===========================================
    //==================================================================================================================




    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode levant une exception si le mot de passe donne en parametre est errone.</p>
     *
     * <ul>
     *      <li><b>@param Compte $user</b>              L'utilisateur.</li>
     *      <li><b>@param string $password</b>          Mot de passe de l'utilisateur.</li>
     *
     * <br>
     *
     *      <li><b>@throws AFE</b>                      L'exception lancee lorsque
     *                                                  le mot de passe est errone.</li>
     * </ul>
     */
    public static function isGoodPassword(Compte $user, string $password){
        //Si le mot de passe est incorrect, on lance une exception.
        if(!password_verify($password, $user->mdp))
            throw new AFE();
    }




    //==================================================================================================================
    //============================== AJOUT, MODIFICATION, ET SUPPRESSION D'UTILISATEUR =================================
    //==================================================================================================================




    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour creer un nouvel utilisateur.</p>
     *
     * <ul>
     *      <li><b>@param string $username</b>          Pseudo de l'utilisateur.</li>
     *      <li><b>@param string $password</b>          Mot de passe de l'utilisateur.</li>
     *      <li><b>@param string $name</b>              Nom de l'utilisateur.</li>
     *      <li><b>@param string $firstName</b>         Prenom de l'utilisateur.</li>
     *      <li><b>@param int $role</b>                 Le role de l'utilisateur.</li>
     *
     * <br>
     *
     *      <li><b>@throws UE</b>                       L'exception lancee pour les pseudos.</li>
     *      <li><b>@throws BPE</b>                      L'exception lancee pour les mot de passe trop faibles.</li>
     * </ul>
     */
    public static function createUser(int $role, string $username, string $password, string $name, string $firstName){
        //Chaque pseudo doit etre unique, si le pseudo existe deja, on lance une exception.
        if(Compte::where('pseudo','=',$username)->count() === 1)
            throw new UE(UE::USERNAME_ALREADY_EXIST);

        //Si le pseudo contient un ou des espace(s), on lance une exception.
        if(preg_match('* *', $username))
            throw new UE(UE::SPACE_FORBIDDEN);

        //Si le pseudo est identique au mot de passe, on lance une exception.
        if($username === $password)
            throw new BPE(BPE::SAME_VALUE_AS_USERNAME_AND_PASSWORD);

        //Si le mot de passe fait moins de 8 caracteres, on lance une exception.
        if(strlen($password) < 8)
            throw new BPE(BPE::TOO_SHORT_PASSWORD);

        //Si le mot de passe ne contient pas au moins une majuscule, on lance une exception.
        if(!preg_match('*[A-Z]*', $password))
            throw new BPE(BPE::PASSWORD_WITHOUT_UPPERCASE);

        //Si le mot de passe ne contient pas au moins un chiffre, on lance une exception.
        if(!preg_match('*[0-9]*', $password))
            throw new BPE(BPE::PASSWORD_WITHOUT_NUMBER);

        //Creation du nouvel utilisateur dans la base de donnees.
        $user = new Compte();
        $user->pseudo = $username;
        $user->mdp = password_hash($password, PASSWORD_DEFAULT);
        $user->prenom = $firstName;
        $user->nom = $name;
        $user->role_id = Role::where('droits', '=', $role)->first()->id;
        //Appel a la methode save() du patron ActiveRecord de Eloquent.
        //Insertion du tuple dans la table.
        $user->save();

        //Un internaute est directement connecte a son compte a la suite de son inscription.
        self::loadProfile($user);
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour modifier un compte d'utilisateur.</p>
     *
     * <ul>
     *      <li><b>@param string $password</b>          Le nouveau mot de passe de l'utilisateur.</li>
     *      <li><b>@param string $name</b>              Le nouveau nom de l'utilisateur.</li>
     *      <li><b>@param string $firstName</b>         Le nouveau prenom de l'utilisateur.</li>
     *
     * <br>
     *
     *      <li><b>@throws BPE</b>                      L'exception lancee pour les mot de passe trop faibles.</li>
     * </ul>
     */
    public static function setUser(string $password, string $name, string $firstName){
        //Si un utilisateur est connecte.
        if(self::isConnected()){
            $user = Compte::where('no','=',$_SESSION['user'])->first();

            //Si le pseudo est identique au mot de passe, on lance une exception.
            if($user->pseudo === $password)
                throw new BPE(BPE::SAME_VALUE_AS_USERNAME_AND_PASSWORD);

            //Si le mot de passe fait moins de 8 caracteres, on lance une exception.
            if(strlen($password) < 8)
                throw new BPE(BPE::TOO_SHORT_PASSWORD);

            //Si le mot de passe ne contient pas au moins une majuscule, on lance une exception.
            if(!preg_match('*[A-Z]*', $password))
                throw new BPE(BPE::PASSWORD_WITHOUT_UPPERCASE);

            //Si le mot de passe ne contient pas au moins un chiffre, on lance une exception.
            if(!preg_match('*[0-9]*', $password))
                throw new BPE(BPE::PASSWORD_WITHOUT_NUMBER);

            $user->mdp = password_hash($password, PASSWORD_DEFAULT);
            $user->prenom = $firstName;
            $user->nom = $name;
            //Appel a la methode save() du patron ActiveRecord de Eloquent.
            //Mise a jour du tuple dans la table.
            $user->save();
        }
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour supprimer le compte de l'utilisateur connecte.</p>
     *
     * <ul>
     *      <li>b>@param string $password</b>           Le mot de passe a verifier</li>
     *
     * <br>
     *
     *      <li><b>@throws AFE</b>                      L'exception lancee lorsque
     *                                                  le mot de passe est errone.</li>
     * </ul>
     */
    public static function deleteUser(string $password){
        //Si un utilisateur est connecte.
        if(self::isConnected()){
            $user = Compte::where('no','=',$_SESSION['user'])->first();

            //Verification du mot de passe.
            self::isGoodPassword($user, $password);
            self::disconnect();



            $user_lists = $user->listes;
            foreach($user_lists as $idxList => $list){
                //On supprime tous les items de la liste.
                $items = Item::where('liste_id', '=', $list->no)->get();
                foreach($items as $idxItem => $item)
                    //Appel a la methode delete() du patron ActiveRecord de Eloquent.
                    //Suppression du tuple dans la table.
                    $item->delete();

                //On supprime tous les messages de la liste.
                $messages = Message::where('liste_id', '=', $list->no)->get();
                foreach($messages as $idxMessage => $message)
                    //Appel a la methode delete() du patron ActiveRecord de Eloquent.
                    //Suppression du tuple dans la table.
                    $message->delete();

                //On supprime la liste.
                //Appel a la methode delete() du patron ActiveRecord de Eloquent.
                //Suppression du tuple dans la table.
                $list->delete();
            }

            //On supprime le compte de l'utilisateur.
            //Appel a la methode delete() du patron ActiveRecord de Eloquent.
            //Suppression du tuple dans la table.
            $user->delete();
        }
    }



    //==================================================================================================================
    //================================ AUTHENTIFICATION ( = CONNEXION ) ET DECONNEXION =================================
    //==================================================================================================================



    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour verifier les identifiants de connexion entres par l'utilisateur.
     * C-a-d pour se <b>connecter</b>.</p>
     *
     * <p>Dans un souci de securite, on prefere ne pas preciser si c'est le mot de passe
     * ou le pseudo qui est errone ou meme les deux. C'est la meme exception qui est
     * lancee a chaque fois. Cela dans le but de decourager les gens mal intentionnes.</p>
     *
     * <ul>
     *      <li><b>@param string $username</b>          Le pseudo de l'utilisateur.</li>
     *      <li><b>@param string $password</b>          Le mot de passe de l'utilisateur.</li>
     * <br>
     *      <li><b>@throws AFE</b>                      L'exception lancee lorsque le pseudo ou
     *                                                  le mot de passe est errone.</li>
     * </ul>
     */
    public static function authenticate(string $username, string $password){
     //Si le pseudo n'exitse pas, on lance une exception.
     if(Compte::where('pseudo','=',$username)->count() === 0)
         throw new AFE();

     $user = Compte::where('pseudo','=',$username)->first();
     //Verification du mot de passe.
     self::isGoodPassword($user, $password);

     //On se connecte.
     self::loadProfile(Compte::where('pseudo','=',$username)->first());
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour se <b>deconnecter</b>.</p>
     *
     */
    public static function disconnect(){
        //Si un utilisateur est connecte.
        if(self::isConnected())
           unset($_SESSION['user']);
    }



    //==================================================================================================================
    //================================= CHARGEMENT D'UN PROFILE D'UTILISATEUR ==========================================
    //==================================================================================================================


    /**
     * [ METHODE INTERNE DE CLASSE. ]
     *
     * Methode pour charger l'utilisateur qui vient de se connecter.
     *
     * @param compte $user      L'utilisateur dans la table compte.
     */
    private static function loadProfile(Compte $user){
        //On retient le nouvel utilisateur.
        $_SESSION['user'] = $user->no;

        //Pour eviter a l'utilisateur de devoir taper son nom lorsqu'il reserve un item.
        //Car l'utilisateur peut soit en tant que createur reserver tout de meme des items
        //de liste dont il n'est pas le createur, et les participant peuvent reserver
        //n'importe quel item de n'importe quelle liste.
        if($user->nom != null and $user->prenom != null)
            $_SESSION['participant'] = strtoupper($user->nom) . " " . $user->prenom;
        else{
            if($user->nom != null)
                $_SESSION['participant'] = strtoupper($user->nom);
            else if($user->prenom != null)
                    $_SESSION['participant'] = $user->prenom;
                 else
                    $_SESSION['participant'] = $user->pseudo;
        }
    }


    //==================================================================================================================
    //======================== VERIFIER LES DROITS D'UN UTILISATEUR A ACCEDER A UNE RESSOURCE ==========================
    //==================================================================================================================



    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Cette methode a pour but de savoir si l'utilisateur a les droits d'acces
     * sur une ressource dont le niveau de permissions requis nous est precise en parametre.</p>
     *
     * <ul>
     *      <li><b>@param int $required</b>         Le niveau de permission requis pour acceder
     *                                              a une ressource.</li>
     *      <li><b>@return bool</b>                 Vaut true si permissions requises correctes.</li>
     * </ul>
     */
    public static function checkAccessRights(int $required) : bool {
       return Compte::where('no','=',$_SESSION['user'])
               ->first()
               ->role
               ->droits
               >=
               $required;
    }



    //==================================================================================================================
    //==================== "SAUVER", IDENTIFIER ET CONNAITRE LES DROITS D'UN CREATEUR DE LISTE =========================
    //==================================================================================================================


    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode permettant de "<b>sauver</b>" le lien entre le proprietaire
     * ( = createur ) de la liste nouvellement cree et la liste elle-meme.</p>
     *
     * <ul>
     *      <li><b>@param Liste $list</b>       La liste en question.</li>
     * </ul>
     */
    public static function saveOwner(Liste $list){
        if(self::isConnected())
            $list->user_id = $_SESSION['user'];
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode indiquant si l'utilisateur actuellement connecte est proprietaire
     * de la liste de souhaits passee en parametre.</p>
     *
     * <ul>
     *      <li><b>@param Liste $list</b>       La liste en question.</li>
     * <br>
     *      <li><b>@return bool</b>             Vaut true si un utilisateur est connecte et
     *                                          qu'il est proprietaire de la liste.</li>
     * </ul>
     */
    public static function isOwnerOfList(Liste $list) : bool {
        return
            (
                self::isConnected()
                and
                $_SESSION['user'] === $list->user_id
            );
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode indiquant si l'utilisateur actuellement connecte a le droit de connaitre
     * les informations reatives a la reservation d'un item d'une liste dont il est proprietaire.</p>
     *
     * <p>Pour avoir ce droit, il doit etre proprietaire de la liste et la liste doit avoir expiree.</p>
     *
     * <ul>
     *      <li><b>@param Liste $list</b>       La liste en question.</li>
     * <br>
     *      <li>b>@return bool</b>             Vaut true si un utilisateur est connecte,
     *                              qu'il est proprietaire de la liste
     *                              et que la liste a expiree.</li>
     * </ul>
     */
    public static function haveRightsToKnow(Liste $list) : bool {
        return
            (
               self::isOwnerOfList($list)
                and
                (
                    (
                        // La methode strtotime(...) rerourne le timestamp Unix
                        // (le nombre de secondes depuis le 1er Janvier 1970)
                        strtotime($list->expiration)
                        -
                        // La fonction date(...) retourne la date actuelle.
                        // On souhaite recuperer la date actuelle au format de
                        // la date d'expiration de la liste soit 'YYYY-MM-DD' en mysql
                        // (format anglais des dates), or ce format s'ecrit differemment
                        // en parametre de date(...), il s'ecrit ainsi :
                        // Y = format annee avec 4 chiffres.
                        // m = format mois : 01-12.
                        // d = format jour : 01-31.
                        strtotime(date('Y-m-d'))
                    )
                    <=0
                )
            );
    }


    //==================================================================================================================
    //==================================================================================================================
}