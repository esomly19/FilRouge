<?php

namespace App\Middleware;

class Middleware{

    protected $container;

    public function __contruct($container){
        $this->container = $container;
    }

}