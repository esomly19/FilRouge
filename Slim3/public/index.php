<?php
require '../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'debug' => true,
        'displayErrorDetails' => true
    ]
]);

require '../src/container.php';
new \App\Database\Capsule;
$app->get('/', \App\controllers\pagesController::class . ':home');

$app->get('/index', \App\controllers\pagesController::class . ':index')->setName('perso');

// CRÉATION PERSO
$app->get('/creer', \App\controllers\pagesController::class. ':creer')->setName('creation');
$app->post('/creer', \App\controllers\pagesController::class. ':creerPerso');

// VOIR TOUS LES PERSOS
$app->get('/liste', \App\controllers\pagesController::class. ':liste')->setName('liste');

// VOIR DETAIL D'UN PERSO
$app->get('/detail/{id}', \App\controllers\pagesController::class. ':detail')->setName('detail');

// SUPPRIMER UN PERSO
//$app->get('/liste/{id}', \App\controllers\pagesController::class. ':supprimer')->setName('supprimer');
$app->post('/liste', \App\controllers\pagesController::class. ':supprimer')->setName('supprimer');



$app->get('/modifier/{id}', \App\controllers\pagesController::class. ':modifier');
$app->post('/modifier/{id}', \App\controllers\pagesController::class. ':updatePerso');
$app->run();


/*
class DemoMiddleware {

  public function __invoke($request, $response,$next) {
    $response->write("<h1>Bienvenue</h1>");
    return $next($request, $response);

  }
*/
 ?>
