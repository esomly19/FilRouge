<?php



require_once '../vendor/autoload.php';
require_once  'config/config.inc.php';
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Super_Street_Dora_Grand_Championship_Turbo\controllers\ControleurHome as CH;

use Slim as slim;



$container = array();

$container["settings"]=$config;

$container['view'] = function ($container){

    $view = new \Slim\views\Twig('../views',[]);

    $router = $container->get('router');
    $uri = \Slim\Http\Uri::creaeFromEnvironment(new \SLim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router,$uri));

    return $view;
};

$container['db'] = function ($container) {

    $capsule = new \Illuminate\Datphpabase\Capsule\Manager;
    $capsule->addConnection($container['setting']['db']);

};


$app = new slim\App();

$app->get('/', function(){
    CH::goHome();
});

$app->run();
