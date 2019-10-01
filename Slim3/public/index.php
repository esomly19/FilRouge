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
$app->get('/perso', \App\controllers\pagesController::class . ':getPerso')-> setName('perso');
$app->post('/perso', \App\controllers\pagesController::class . ':postPerso');
// $app->get('/', 'App\Controller\HomeController:index');
$app->get('/index', \App\controllers\pagesController::class . ':index');



$app->run();


/*
class DemoMiddleware {

  public function __invoke($request, $response,$next) {
    $response->write("<h1>Bienvenue</h1>");
    return $next($request, $response);

  }
*/
 ?>
