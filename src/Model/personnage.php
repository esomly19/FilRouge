<?php

namespace App\Model;

use App\Database;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personnage extends \Illuminate\Database\Eloquent\Model {
    protected   $id,
    $prenom,
    $nom,
    $poids,
    $taille,
    $vie,
    $attaque,
    $defense,
    $agilite,
    $photo,
    $deleted_at;
  
    protected $table = 'ProjetFilRouge_Personnage';

    use SoftDeletes;

    public $timestamps = false;



    public function getId() {
        return $this->id;
    }
    
    public function getPrenom() {
        return $this->prenom;
    }
    public function getNom() {
        return $this->nom;
    }
    public function getTaille() {
        return $this->taille;
    }
    public function getVie() {
        return $this->Vie;
    }
    
    public function getAttaque() {
        return $this->attaque;
    }
    
    public function getDefense() {
        return $this->defense;
    }
    
    public function getAgilite() {
        return $this->agilite;
    }
    public function getPhoto() {
        return $this->photo;
    }
    public function setNom($nom) {
        if (is_string($nom)) {     // Vérification si présence d'une chaîne de caractères
            $this->nom = $nom;    // On assigne alors la valeur $nom à l'attribut _nom
        }
    }
    
    public function setDegats($degats) {
        $degats = (int)$degats; // Conversion de l'argument en nombre entier
        // Vérification - Le nombre doit être strictemeznt positif et compris entre 0 et 100
        if ($degats >= 0 && $degats <= 100) {
            $this->degats = $degats; // on assigne alors la valeur $degats à l'attribut _degats
        }
    }
    
}
