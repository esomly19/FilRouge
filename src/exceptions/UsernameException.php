<?php

namespace Super_Street_Dora_Grand_Championship_Turbo\exceptions;
final class UsernameException extends \Exception
{
    /**
     * [ CONSTANTES DE CLASSE. ]
     *
     * Messages d'exception possibles.
     */
    const USERNAME_ALREADY_EXIST = "Ce pseudo existe deja !";
    const SPACE_FORBIDDEN = "Un pseudo ne peut pas contenir d'espace !";

    /**
     * <h3>[ CONSTRUCTEUR. ]</h3>
     *
     * <p>Constructeur de l'exception personalisee relative a la violation de la contrainte
     * d'unicité sur le pseudo.</p>
     *
     * <ul>
     *      <li><b>@param string $exception_message</b>        Le message que l'exception doit renvoyer.</li>
     * </ul>
     */
    public function __construct(string $exception_message){
        parent::__construct($exception_message);
    }
}