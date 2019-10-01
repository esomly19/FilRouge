<?php

require_once 'vendor/autoload.php';

use \App\Model\Characters as personnage;
use \App\Model\Monsters as monstre;
use \App\Model\Battles as combat;


$app = new \Slim\App([
    'settings' => [
        'debug' => true,
        'displayErrorDetails' => true
    ]
]);

require './src/container.php';
new \App\Database\Capsule;

$personnages = personnage::all();
echo "test de donnée des personnage <br><br>";
foreach($personnages as $personnage){

    echo "nom : ". $personnage->nom ." prenom :". $personnage->prenom."<br>";
}


echo "<br>test de donnée de combat<br><br>";

$combat = combat::where('id','=',1);

$pers = personnage::where('id','=','$combat.pers');
$monstre = monstre::where('id','=','$combat.monstre');

var_dump($pers);