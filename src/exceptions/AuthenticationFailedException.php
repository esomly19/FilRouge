<?php

namespace Super_Street_Dora_Grand_Championship_Turbo\exceptions;
final class AuthenticationFailedException extends \Exception{
    /**
     * <h3>[ CONSTRUCTEUR. ]<h3>
     *
     * <p>Constructeur de l'exception personalisee relative aux identifiants de connexion incorrects.</p>
     */
    public function __construct(){
        parent::__construct("Pseudo et/ou mot de passe incorrect(s) !");
    }
}