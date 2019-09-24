<?php


namespace Super_Street_Dora_Grand_Championship_Turbo\views;

use Super_Street_Dora_Grand_Championship_Turbo\models\Compte as Compte;

use Slim\Slim as Slim;

use Super_Street_Dora_Grand_Championship_Turbo\rights\Authentication as A;

final class DisplayUserView extends SuperclassView
{
    /**
     * [ CONSTRUCTEUR. ]
     *
     * <p>Constructeur de <i> DisplayUserView</i>. Il definit le code html et css propre
     * a la vue <i> DisplayUserView</i>, par l'intermediaire des attributs de SuperclassView
     * dont la classe herite.</p>
     */
    public function __construct(){
        //================================== CSS =======================================================================
        $this->css = "User.css";

        //================================== TITLE =====================================================================
        $this->title = " - Mon Compte";

        //================================== BODY ======================================================================
        $user = Compte::where('no','=',$_SESSION['user'])->first();

        $this->body = "
        <div id=count>
            <p class=under_space>
                Pseudo : 
                <b>
                   $user->pseudo
                </b>
                <br>
                <br>";

        if($user->nom != null)
            $this->body .= "
                Nom : 
                <b>
                    $user->nom
                </b>";

        if($user->nom != null and $user->prenom != null)
            $this->body .= "
                <br>
                <br>";

        if($user->prenom != null)
           $this->body .= "
                Prenom : 
                <b>
                    $user->prenom
                </b>";

        $this->body .= "
            </p>";

        //Seul un utilisateur authentifie de type createur
        //peut joindre une liste a son compte, a l'inverse
        //des utilisateurs authentifies de type participant.
        if(A::checkAccessRights(A::CREATOR_RIGHTS_LEVEL)) {
          $urlJoin = Slim::getInstance()->urlFor('JOIN');
          $this->body .= "  
            <form class=under_space method=post action=$urlJoin>
                <label>Joindre une liste a mon compte ?</label>
                <br>
                <br>
                <label>numero de la liste :</label>
                <br>
                <input type=text name=no placeholder=667 required> 
                <br>
                <br>
                <label>token de la liste :</label>
                <br>
                <input type=text name=token placeholder=6YYYUU6PJFIHDNZO7 required> 
                <br>
                <br>
                <button type=submit>joindre</button>
            </form>";
      }

        $urlDelete = Slim::getInstance()->urlFor('DELETE_USER');
        $urlSet =  Slim::getInstance()->urlFor('OPEN_USER_EDITOR');
        $this->body .= "  
            <form class=under_space method=post action=$urlSet>
                <label>Modifier mes informations personnelles ?</label>
                <br>
                <br>
                <label>mot de passe :</label>
                <br>
                <input type=password name=mdp required> 
                <br>
                <br>
                <button type=submit>modifier</button>
            </form>
            <form class=under_space method=post action=$urlDelete>
                <label>Supprimer mon compte ?</label>
                <br>
                <br>
                <label>mot de passe :</label>
                <br>
                <input type=password name=mdp required> 
                <br>
                <br>
                <br>
                <button type=submit>supprimer</button>
            </form>
        </div>";
    }
}