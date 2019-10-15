<?php
namespace App\Model;
use App\Database;
class Monsters extends \Illuminate\Database\Eloquent\Model {
    
    protected $table = 'ProjetFilRouge_Monstre';

    use SoftDeletes;

    function battle(){
        return $this->hasMany('App\Model\Battles','id','monstre');
    }
    
}