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
    
    public function getDegats() {
        return $this->degats;
    }
    
    public function getTimeToBeAsleep() {
        return $this->timeToBeAsleep;
    }
    
    public function getType() {
        return $this->type;
    }
    
    public function getAtout() {
        return $this->atout;
    }
    
}
