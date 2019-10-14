<?php



require_once 'vendor/autoload.php';
use Slim as slim;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as Capsule;


use Super_Street_Dora_Grand_Championship_Turbo\controllers\ControleurHome as CH;
use Super_Street_Dora_Grand_Championship_Turbo\controllers\CreatorController as CC;
use Super_Street_Dora_Grand_Championship_Turbo\controllers\UserController as UC;
use Super_Street_Dora_Grand_Championship_Turbo\views\login as login;

$capsule = new Capsule;

$capsule->addConnection(parse_ini_file('src/config/conf.ini'));
$capsule->setAsGlobal();
$capsule->bootEloquent();




session_start();
/*

$container = array();

$container["settings"]=$config;

$container['view'] = function ($container){
 courrier concernant votre candidature à la formation 'LP Administration des systèmes réseaux et applications à base de logiciels libres (Formation initiale ou en alternance)'
Vous pouvez également vous connecter à votre espace personnel afin de visualiser votre 
    $view = new \Slim\views\Twig('/views',[]);

    $router = $container->get('router');
    $uri = \Slim\Http\Uri::creaeFromEnvironment(new \SLim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router,$uri));

    return $view;
};

$container['db'] = function ($container) {

    $capsule = new \Illuminate\Datphpabase\Capsule\Manager;
    $capsule->addConnection($container['setting']['db']);

};

*/
$app = new slim\App();

$app->get('/', function(){
    CH::goHome();
});


/*
//Aller a page de "mon" compte.
$app->get('/view/user', function(){
    UC::goCount();
})->name('VIEW_USER');

*/
// S'authentifier en tant qu'utilisateur.
$app->post('/connected', function(){
    UC::connect();
});
//->name('CONNECT');
/*
//Se deconnecter.
$app->get('/disconnected', function(){
    UC::disconnect();
})->name('DISCONNECT');


//Afficher le formulaire pour la creation de compte d'utilisateur.
$app->get('/add/user/:role', function(int $role){
    UC::openUserCreator($role);
})->name('OPEN_USER_CREATOR');

//Ajouter un utilisateur.
$app->post('/add/user/:role', function(int $role){
    UC::addUser($role);
})->name('ADD_USER');
*/

$app->run();
