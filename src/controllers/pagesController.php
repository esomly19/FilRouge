<?php

namespace App\controllers;

use App\Database;
use App\Model\personnage;

class pagesController {

    public function __construct($container)
    {
      $this->container = $container;
    }

    public function home($request, $response)
    {
      $this->container->view->render($response, 'pages/home.html.twig');
    }

// ---------------- Voir tous les persos -------------------
    public function liste($request, $response, $args)
    {
      $perso = Personnage::all();
      $this->container->view->render($response, 'pages/liste.html.twig', ['personnages'=>$perso]);
    }

// -------------- Voir détail d'un perso ---------------------
    public function detail($request, $response, $args)
    {
      $perso = Personnage::find(intVal($args['id']));
      $this->container->view->render($response, 'pages/detail.html.twig', ['personnages'=>$perso]);
    }

// ------------- Supprime un perso -------------------------
    public function supprimer($request, $response, $args){
      $perso = Personnage::find($_POST["id"]);
      //var_dump(Personnage::find(intVal($args['id'])));
      $perso->delete();
      $perso = Personnage::all();
      $this->container->view->render($response, 'pages/liste.html.twig', ['personnages'=>$perso]);
    }

// ----------- Créer un perso -----------------------


    public function creer($request, $response, $args){  
    //  $perso = Users::find(intVal($args['id']));
      $this->container->view->render($response, 'pages/createPerso.html.twig');
    }

    
    public  function creerPerso($request, $response, $args)
    {
  /*    $perso = Users::find(intVal($_POST["id"]));*/
      $perso = new Personnage();
      $perso->prenom = $_POST["prenom"];
      $perso->nom = $_POST["nom"];
      $perso->poids = $_POST["poids"];
      $perso->taille = $_POST["taille"];
      $perso->vie = $_POST["vie"];
      $perso->attaque = $_POST["attaque"];
      $perso->defense = $_POST["defense"];
      $perso->agilite = $_POST["agilite"];
      $perso->photo = $_POST["photo"];
      $perso->save();
      $perso = Personnage::all();
      $this->container->view->render($response, 'pages/liste.html.twig', ['personnages'=>$perso]);
    }


    // ----------- Modifie un perso -----------------------

    public function modifier($request, $response, $args){
      $perso = Personnage::find(intVal($args['id']));
      $this->container->view->render($response, 'pages/updatePerso.html.twig', ['personnages'=>$perso]);
    }

    public  function updatePerso($request, $response, $args)
    {
      $perso = Personnage::find(intVal($args['id']));
      $perso->prenom = $_POST["prenom"];
      $perso->nom = $_POST["nom"];
      $perso->poids = $_POST["poids"];
      $perso->taille = $_POST["taille"];
      $perso->vie = $_POST["vie"];
      $perso->attaque = $_POST["attaque"];
      $perso->defense = $_POST["defense"];
      $perso->agilite = $_POST["agilite"];
      $perso->photo = $_POST["photo"];
      $perso->save();
      $perso = Personnage::all();
      $this->container->view->render($response, 'pages/liste.html.twig', ['personnages'=>$perso]);
    }

}

 ?>
