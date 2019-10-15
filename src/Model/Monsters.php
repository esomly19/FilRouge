<?php
namespace App\Model;
use App\Database;
use Illuminate\Database\Eloquent\SoftDeletes;

class Monsters extends \Illuminate\Database\Eloquent\Model {
    
    protected $table = 'ProjetFilRouge_Monstre';

    use SoftDeletes;

    public $timestamps = false;

    function battle(){
        return $this->hasMany('App\Model\Battles','id','monstre');
    }

}