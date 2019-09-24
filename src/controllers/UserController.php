<?php
namespace Super_Street_Dora_Grand_Championship_Turbo\controllers;

use Slim as slim;

use Super_Street_Dora_Grand_Championship_Turbo\views\HomeView as HV;

use Super_Street_Dora_Grand_Championship_Turbo\views\StatusView as SV;
use Super_Street_Dora_Grand_Championship_Turbo\views\CreationUserView as CUV;
use Super_Street_Dora_Grand_Championship_Turbo\views\DisplayUserView as DUV;
use Super_Street_Dora_Grand_Championship_Turbo\views\SetUserView as SUV;



use Super_Street_Dora_Grand_Championship_Turbo\filters\Filter as Filter;

use Super_Street_Dora_Grand_Championship_Turbo\rights\Authentication as A;
use Super_Street_Dora_Grand_Championship_Turbo\rights\CheckRights as CR;

use Super_Street_Dora_Grand_Championship_Turbo\models\Liste as Liste;
use Super_Street_Dora_Grand_Championship_Turbo\models\Compte as Compte;

final class UserController
{

    public static function goCount(){
        (new DUV())->render();
    }



public static function connect(){
    try{
        A::authenticate(
            //Pas besoin de filtrre les donnees ne sero,nt inseres sulle part, elles sont
            //seulement compares a d'autres valeurs en interne.
            Filter::filter(Slim::getInstance()->request->post('pseudo'), Filter::FILTER_STRING),
            Filter::filter(Slim::getInstance()->request->post('mdp'), Filter::FILTER_STRING)
        );
        (new HV(HV::SUCCESS_CONNECTION))->render();
    }catch (AFE $afe){
        (new HV($afe->getMessage()))->render();
    }catch (FDE $fde){
        (new SV(SV::FAILURE_CONNEXION, $fde))->render();
}
}

/**
 * [ METHODE DE CLASSE. ]
 *
 * <p>Methode pour se connecter.<p>
 */
public static function disconnect(){
    A::disconnect();
    (new HV())->render();
}



    //==================================================================================================================
    //==================================== OUVERTURE DU CREATEUR D'UTILISATEUR =========================================
    //==================================================================================================================


    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour generer la page html/css du formulaire permettant aux internautes de se creer des comptes
     * sur le site.</p>
     *
     * <ul>
     *      <li><b>@param int $role</b>     Le role lie a ce compte : participant ou createur.</li>
     * </ul>
     */
    public static function openUserCreator(int $role){
        (new CUV($role))->render();
    }



    //==================================================================================================================
    //====================================== OUVERTURE DE L'EDITEUR DE COMPTE ==========================================
    //==================================================================================================================



    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour generer le formulaire permettant de modifier
     * un compte.</p>
     */
    public static function openUserEditor(){
        try{
            //Verification du password.
            $password = Filter::filter(Slim::getInstance()->request->post('mdp'), Filter::FILTER_STRING);
            A::isGoodPassword(Compte::where('no','=',$_SESSION['user'])->first(), $password);
            (new SUV())->render();
        }catch(AFE $afe) {
            (new SV(SV::FAILURE_MODIFICATION_USER, $afe))->render();
        }catch(FDE $fde) {
            (new SV(SV::FAILURE_MODIFICATION_USER, $fde))->render();
        }
    }


    //==================================================================================================================
    //====================================== AJOUT ET SUPPRESSION D'UTILISATEUR ========================================
    //==================================================================================================================


    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour finaliser la creation du compte d'un internaute. Les donnees fournies sont filtrees
     * puis inserer dans la base de donnees.</p>
     *
     * <ul>
     *      <li><b>i@param int $role<b>     Le role lie au compte : participant ou createur.</li>
     * </ul>
     */
    public static function addUser(int $role){
        try {
            A::createUser(
                $role,
                //Par souci de securite, on filtre les donnees fournies.
                Filter::filter(Slim::getInstance()->request->post('pseudo'), Filter::FILTER_STRING),
                Filter::filter(Slim::getInstance()->request->post('mdp'), Filter::FILTER_STRING),
                Filter::filter(Slim::getInstance()->request->post('nom'), Filter::FILTER_STRING),
                Filter::filter(Slim::getInstance()->request->post('prenom'), Filter::FILTER_STRING)
            );
            (new SV(SV::SUCCESS_CREATION_USER))->render();
        }catch(UE $ue){
            (new CUV($role, $ue))->render();
        }catch(BPE $bpe) {
            (new CUV($role, $bpe))->render();
        }catch(FDE $fde){
            (new SV(SV::FAILURE_CREATION_USER, $fde))->render();
        }
    }

    /**
     * <h3>[ METHODE DE CLASSE. ]</h3>
     *
     * <p>Methode pour finaliser la modification du compte d'un internaute. Les donnees fournies sont filtrees
     * puis inserer dans la base de donnees.</p>
     */
    public static function setUser(){
        try {
            A::setUser(
                //Par souci de securite, on filtre les donnees fournies.
                Filter::filter(Slim::getInstance()->request->post('mdp'), Filter::FILTER_STRING),
                Filter::filter(Slim::getInstance()->request->post('nom'), Filter::FILTER_STRING),
                Filter::filter(Slim::getInstance()->request->post('prenom'), Filter::FILTER_STRING)
            );
            (new SV(SV::SUCCESS_MODIFICATION_USER))->render();
        }catch(BPE $bpe) {
            (new SUV($bpe))->render();
        }catch(FDE $fde){
            (new SV(SV::FAILURE_MODIFICATION_USER, $fde))->render();
        }
    }

    /**
     * [ METHODE DE CLASSE. ]
     *
     * <p>Methode pour supprimer son compte.<p>
     */
    public static function deleteUser(){
        try{
            A::deleteUser(Filter::filter(Slim::getInstance()->request->post('mdp'), Filter::FILTER_STRING));
            (new SV(SV::SUCCESS_DELETION_USER))->render();
        }catch(AFE $afe) {
            (new SV(SV::FAILURE_DELETION_USER, $afe))->render();
        }catch(FDE $fde) {
            (new SV(SV::FAILURE_DELETION_USER, $fde))->render();
        }
    }



    //==================================================================================================================
    //==================================== JOINDRE UNE LISTE A "MON" COMPTE ============================================
    //==================================================================================================================



    /**
     *[ METHODE DE CLASSE. ]
     *
     * Methode pour joindre une liste a un compte d'utilisateur.
     */
    public static function join(){
        try{
            $no = Filter::filter(Slim::getInstance()->request->post('no'), Filter::FILTER_STRING);
            $token = Filter::filter(Slim::getInstance()->request->post('token'), Filter::FILTER_STRING);
            if(CR::checkRightsForList($no, $token)){
                $list = Liste::where('no','=',$no)->first();
                A::saveOwner($list);
                //Appel a la methode save() du patron ActiveRecord de Eloquent.
                //Mise a jour du tuple dans la table.
                $list->save();
            }
            (new SV(SV::SUCCESS_JOIN_LIST))->render();
        }catch(FDE $fde) {
            (new SV(SV::FAILURE_JOIN_LIST, $fde))->render();
        }
    }



    //==================================================================================================================
    //==================================================================================================================
}