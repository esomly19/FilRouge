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

    public function modifier($request, $response, $args){
      $user = Users::find(intVal($args['id']));
      $this->container->view->render($response, 'pages/createPerso.html.twig', ['utilisateurs'=>$user]);
    }

    public function deletePerso($request, $response, $args){
      $id = $args['id'];
      $user = Users::find($id);
      $this->container->view->render($response, 'pages/createPerso.html.twig', ['utilisateurs'=>$user]);
    }

    public  function creerPerso($request, $response, $args)
    {
  /*    $user = Users::find(intVal($_POST["id"]));*/
      $user = new Users();
      $user->prenom = $_POST["prenom"];
      $user->nom = $_POST["nom"];
      $user->poids = $_POST["poids"];
      $user->taille = $_POST["taille"];
      $user->vie = $_POST["vie"];
      $user->attaque = $_POST["attaque"];
      $user->defense = $_POST["defense"];
      $user->agilite = $_POST["agilite"];
      $user->photo = $_POST["photo"];
      $user->save();
      $this->container->view->render($response, 'pages/createPerso.html.twig', ['utilisateurs'=>$user]);
    }
}

 ?>
