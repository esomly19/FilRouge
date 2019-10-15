<?php
namespace App\Model;
use App\Database;
class Characters extends \Illuminate\Database\Eloquent\Model {
    protected $table = 'ProjetFilRouge_Personnage';
    function battle(){
        return $this->hasMany('App\Model\Battles','id','perso');
    }
}