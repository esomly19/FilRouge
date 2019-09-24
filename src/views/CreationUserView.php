<?php


namespace Super_Street_Dora_Grand_Championship_Turbo\views;

use Slim as Slim;

final class CreationUserView extends WorkOnUserView{
    /**
     * <h3>[ CONSTRUCTEUR. ]</h3>
     *
     * <p>Constructeur de <i>CreationUserView</i>. Il definit le code html et css propre
     * a la vue <i>CreationUserView</i>, par l'intermediaire des attributs de SuperclassView
     * dont la classe herite.</p>
     *
     * <ul>
     *      <li><b>@param int $role<b>                      Role lie au compte (participant ou createur).</li>
     *      <li><b>@param \Exception|null $exception<b>     Exception attrapee lorsque le pseudo ou le mot de passe
     *                                                      choisi est incorrect.</li>
     * </ul>
     */
    public function __construct(int $role, \Exception $exception = null){
        parent::__construct(
            Slim::getInstance()->urlFor('ADD_USER', ['role' => $role]),
            "placeholder=dupont",
            "placeholder=didier",
            $exception
        );
    }
}