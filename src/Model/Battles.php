<?php
namespace App\Model;
use App\Database;
class Battles extends \Illuminate\Database\Eloquent\Model {
    protected $table = 'ProjetFilRouge_Combat';
    public $timestamps = false;

    function monstre(){
        return $this->belongsTo('App\Model\Monsters','id','monstre');
    }
    function personnage(){
        return $this->belongsTo('App\Model\Characters','id','perso');
    }
}