<?php

namespace App\Middleware;

class AuthMiddleware extends Middleware{

    public function __invoke($request,$response,$next){

        if(!$this->container->login->isConnected){          
            return $response->withRedirect($this->container->router->pathFor('connexion'));
        }
    }

}