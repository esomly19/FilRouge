<?php

namespace App\Model;

use App\Database;
use Illuminate\Database\Eloquent\SoftDeletes;

class Personnage extends \Illuminate\Database\Eloquent\Model {
    protected $table = 'ProjetFilRouge_Personnage';

    use SoftDeletes;

    public $timestamps = false;
}
