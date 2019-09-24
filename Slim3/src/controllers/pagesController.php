<?php

namespace App\controllers;

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
}

 ?>
