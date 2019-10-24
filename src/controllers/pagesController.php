<?php

namespace App\controllers;

use App\Database;
use App\Model\personnage;
use App\Model\Monsters;
use App\Model\Battles;

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
      $monstre = Monsters::all();      
      $this->container->view->render($response, 'pages/liste.html.twig', ['personnages'=>$perso, 'monstres'=>$monstre]);
    }

// -------------- Voir détail d'un perso ---------------------
    public function detail($request, $response, $args)
    {
      $perso = Personnage::find(intVal($args['id']));
      $this->container->view->render($response, 'pages/detail.html.twig', ['personnages'=>$perso]);
    }

    // -------------- Voir détail d'un monstre ---------------------
    public function detailm($request, $response, $args)
    {
      $monstre = Monsters::find(intVal($args['id']));
      $this->container->view->render($response, 'pages/detailm.html.twig', ['monstres'=>$monstre]);
    }

// ------------- Supprime un perso -------------------------
    public function supprimer($request, $response, $args){
      $perso = Personnage::find($_POST["id"]);
      //var_dump(Personnage::find(intVal($args['id'])));
      $perso->delete();
      $perso = Personnage::all();
      $monstre = Monsters::all();
      $this->container->view->render($response, 'pages/liste.html.twig', ['personnages'=>$perso, 'monstres'=>$monstre]);
    }

    // ------------- Supprime un monstre -------------------------
    public function supprimerm($request, $response, $args){
      $monstre = Monsters::find($_POST["id"]);
      //var_dump(Personnage::find(intVal($args['id'])));
      $monstre->delete();
      $perso = Personnage::all();
      $monstre = Monsters::all();
      $this->container->view->render($response, 'pages/liste.html.twig', ['personnages'=>$perso, 'monstres'=>$monstre]);
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

      if ($_POST["photo"] == ""){
        $perso->photo = "https://vignette.wikia.nocookie.net/animalcrossing/images/8/87/Dora.png/revision/latest?cb=20140323223212&path-prefix=fr";
      }
      else {
        $perso->photo = $_POST["photo"];
      }
      $perso->save();
      $monstre = Monsters::all();   
      $perso = Personnage::all();
      $this->container->view->render($response, 'pages/liste.html.twig', ['personnages'=>$perso,'monstres'=>$monstre]);
    }

    // ----------- Créer un perso -----------------------


    public function creerM($request, $response, $args){  
      //  $perso = Users::find(intVal($args['id']));
        $this->container->view->render($response, 'pages/createMonstre.html.twig');
      }
  
      
      public  function creerMonstre($request, $response, $args)
      {
    /*    $perso = Users::find(intVal($_POST["id"]));*/
        $monstre = new Monsters();
        $monstre->nom = $_POST["nom"];
        $monstre->poids = $_POST["poids"];
        $monstre->taille = $_POST["taille"];
        $monstre->vie = $_POST["vie"];
        $monstre->attaque = $_POST["attaque"];
        $monstre->defense = $_POST["defense"];
        $monstre->agilite = $_POST["agilite"];
        if ($_POST["photo"] == ""){
          $monstre->photo = "http://www.latourdesheros.com/ltdh/images/thumb/c/ca/Dora_Smithy_-_Profil.png/352px-Dora_Smithy_-_Profil.png";
        }
        else {
          $monstre->photo = $_POST["photo"];
        }
        $monstre->save();
        $perso = Personnage::all(); 
        $monstre = Monsters::all();
        $this->container->view->render($response, 'pages/liste.html.twig', ['personnages'=>$perso, 'monstres'=>$monstre]);
      }

    // ----------- Modifie un perso -----------------------

    public function modifperso($request, $response, $args){
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
      $monstre = Monsters::all();
      $this->container->view->render($response, 'pages/liste.html.twig', ['personnages'=>$perso, 'monstres'=>$monstre ]);
    }

    
    // ----------- Modifie un monstre -----------------------

    public function modifierm($request, $response, $args){
      $monstre = Monsters::find(intVal($args['id']));
      $this->container->view->render($response, 'pages/updateMonstre.html.twig', ['monstres'=>$monstre]);
    }

    public  function updateMonstre($request, $response, $args)
    {
      $monstre = Monsters::find(intVal($args['id']));
      $monstre->nom = $_POST["nom"];
      $monstre->poids = $_POST["poids"];
      $monstre->taille = $_POST["taille"];
      $monstre->vie = $_POST["vie"];
      $monstre->attaque = $_POST["attaque"];
      $monstre->defense = $_POST["defense"];
      $monstre->agilite = $_POST["agilite"];
      $monstre->photo = $_POST["photo"];
      $monstre->save();
      $perso = Personnage::all();
      $monstre = Monsters::all();
      $this->container->view->render($response, 'pages/liste.html.twig', ['personnages'=>$perso, 'monstres'=>$monstre ]);
    }

/*---------------------------------------------------*/
    public function choisir($request, $response, $args)
    {
      $perso = Personnage::all();
      $monstre = Monsters::all();   
           $this->container->view->render($response, 'pages/pickthem.html.twig', ['personnages'=>$perso, 'monstres'=>$monstre]);
    }


    public function combat($request, $response, $args){
      
     // $idmonstre = Monsters::find($_POST["monstre"]);
     $idmonstre = (intVal($args['idm']));
     $idperso = (intVal($args['idp']));
      //$idperso = Personnage::find($_POST["perso"]);
      $perso = Personnage::where('id','=',$idperso)->first();
      $monstre = Monsters::where('id','=',$idmonstre)->first();
      $personne = (intVal($args['idp']));

      // COMBAT
      $combat = new Battles();
      $combat->perso = $idperso;
      $combat->monstre = $idmonstre;
      $combat->save();

      // QUI COMMENCE
      $agip = $perso->agilite ;
      $agim = $monstre->agilite;
      if ($agip>$agim) {
        $premier = $perso;
      }
      else $premier = $monstre;

      // Degat infliger par le perso 
      $degatp = $perso->attaque;
      $degatm = $monstre->attaque;

      // vie pour après
      $viep = $perso->vie;
      $viem = $monstre->vie;

      // Nombre de tour 
      $nbtour=0;
      
      while($viem >=0 && $viep >= 0){
          if ($premier==$perso){
            $viem = $viem - $degatp;
            $nbtour++;
            if ($viem <= 0) {
            $gagnant = $perso; 
            }
          }
            else {
              $viep = $viep - $degatm;
              $nbtour++;
              if ($viep <= 0) {
                $gagnant = $monstre;
              }
          
          }
        }
          
        
        
  
/*
        if ($premier=$perso){
          $viem = $viem - $degatp;
        }

      }
      while ($viep != 0 || $viem != 0) {
          $viem = $viem - $degatp;
          $viep = $viep - $degatm;
      }
      */
          /*
        } 
         $nbtour++;         
     } 
     */
     
    // gagnant
    /*
    if ($viep <= 0) {
      $gagnant = $perso->nom;
    }
    else if ($viem <= 0) {
      $gagnant = $monstre->nom;
    }
*/
      $this->container->view->render($response, 'pages/combat.html.twig', ['personnage'=>$perso, 'monstre'=>$monstre, 'nbtour'=>$nbtour,'premier'=>$premier, 'gagnant'=>$gagnant, 'degatp'=>$degatp]);
    }

}


 ?>
