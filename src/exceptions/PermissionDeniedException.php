<?php

namespace Super_Street_Dora_Grand_Championship_Turbo\exceptions;
final class PermissionDeniedException extends \Exception
{
    /**
     * <h3>[ CONSTRUCTEUR. ]</h3>
     *
     * <p>Constructeur de l'exception personalisee relative aux permissions non accordees.</p>
     */
    public function __construct(){
        parent::__construct("Persmissions d'acces a la ressource non accordees !");
    }
}