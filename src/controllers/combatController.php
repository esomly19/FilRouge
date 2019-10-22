<?php

namespace App\controllers;

use App\Database;
use App\Model\personnage;
use App\Model\Monsters;

class pagesController {

    public function __construct($container)
    {
      $this->container = $container;
    }

    public function degat() {
      return 5 ;
    }

}