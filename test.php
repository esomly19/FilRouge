<?php
require_once 'vendor/autoload.php';
use \App\Model\personnage as personnage;
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

echo "test cherche le monstre 33<br><br>";



$monstre12 = monstre::where('id','=',12)->first();

    echo $monstre12->nom ."<br><br>";





echo "test de donnée des personnage <br><br>";


$personnages = personnage::all();

foreach($personnages as $personnage){
    echo "nom : ". $personnage->nom ." prenom :". $personnage->prenom."<br>";
}

echo "<br>test de donnée de combat<br><br>";

$combat = combat::where('id','=',1)->first();

$pers = personnage::where('id','=',$combat->perso)->first();
$monstre = monstre::where('id','=',$combat->monstre)->first();


echo $personnage->nom ." ". $personnage->prenom." VS " . $monstre->nom . "<br>";
if($combat->resultat === 0){
    echo "Vainqueur :" .  $personnage->nom ." ". $personnage->prenom."<br><br>";
}
else{
    echo "Vainqueur :" .  $monstre->nom ."<br><br>";
}
