<?php

namespace App\controllers;

use App\Database;
use App\Model\Users;

class pagesController {

    public function __construct($container)
    {
      $this->container = $container;
    }

    public function home($request, $response)
    {
      $this->container->view->render($response, 'pages/home.html.twig');
    }

    public function getPerso($request, $response)
    {
      $this->container->view->render($response, 'pages/createPerso.html.twig');
    }

    public function postPerso($request, $response)
    {
      var_dump($request->getParams());
      $this->container->view->render($response, 'pages/createPerso.html.twig');
    }


    function index($request, $response, $args)
    {
  //  $user = Users::find(1);
    $user = Users::all();
    $this->container->view->render($response, 'pages/home.html.twig', ['utilisateurs'=>$user]);
  //  $response->write($user->nom);
    }
}

 ?>
