<?php
require '../vendor/autoload.php';

use App\Middleware\AuthMiddleware;

$app = new \Slim\App([
    'settings' => [
        'debug' => true,
        'displayErrorDetails' => true
    ]
]);

session_start();

require '../src/container.php';
new \App\Database\Capsule;

$container = $app->getContainer();

$app->get('/', \App\controllers\pagesController::class . ':home')->setName('home');

$app->get('/accueil', \App\controllers\loginController::class . ':accueil')->setName('connexion');

$app->get('/login', \App\controllers\loginController::class . ':seConnecter')-> setName('login');
$app->post('/login', \App\controllers\loginController::class . ':seConnecter');

$app->get('/choisir', \App\controllers\pagesController::class . ':choisir')->setName('choix');
$app->post('/choisir', \App\controllers\pagesController::class . ':choisir');

$app->get('/disconnect', \App\controllers\loginController::class . ':disconnect')->setName('deconnection');

$app->get('/combat{idp}VS{idm}', \App\controllers\pagesController::class . ':combat')->setName('combat');

$app->group('', function() {
  // CRÉATION PERSO
  $this->get('/creer', \App\controllers\pagesController::class. ':creer')->setName('creation');
  $this->post('/creer', \App\controllers\pagesController::class. ':creerPerso');

  // CRÉATION Monstres
  $this->get('/creerM', \App\controllers\pagesController::class. ':creerM')->setName('monstres');
  $this->post('/creerM', \App\controllers\pagesController::class. ':creerMonstre');

  // VOIR TOUS LES PERSOS
  $this->get('/liste', \App\controllers\pagesController::class. ':liste')->setName('liste');

  // VOIR DETAIL D'UN PERSO
  $this->get('/detail/{id}', \App\controllers\pagesController::class. ':detail')->setName('detail');

  // VOIR DETAIL D'UN Monstre     
  $this->get('/detailm/{id}', \App\controllers\pagesController::class. ':detailm')->setName('detailm');

  // SUPPRIMER UN PERSO
  $this->post('/liste', \App\controllers\pagesController::class. ':supprimer')->setName('supprimer');

  // SUPPRIMER UN monstre
  $this->post('/ok_liste', \App\controllers\pagesController::class. ':supprimerm')->setName('supprimerm');

  // MODIFIER UN PERSO
  $this->get('/modifperso{id}', \App\controllers\pagesController::class. ':modifperso')->setName('modifperso');
  $this->post('/modifperso{id}', \App\controllers\pagesController::class. ':updatePerso');

  // MODIFIER UN Monstre
  $this->get('/modifierm{id}', \App\controllers\pagesController::class. ':modifierm')->setName('modifierm');
  $this->post('/modifierm{id}', \App\controllers\pagesController::class. ':updateMonstre');

  //AFFICHE la liste des combats
  $this->get('/comb', \App\controllers\pagesController::class . ':liscomb')->setName('liscombat');
  $this->post('/comb', \App\controllers\pagesController::class . ':liscomb');

  $this->get('/combat{idp}{idm}', \App\controllers\pagesController::class . ':combat')->setName('combat');

  $this->get('/create', \App\controllers\loginController::class . ':voir');
  $this->post('/create', \App\controllers\loginController::class . ':creerUtilisateur')->setName('creercompte');
})/*->add(new AuthMiddleware($container))*/;
/**
 * le middleware génére des erreurs meme dans le cas ou on est connecte 
 * je ne l'ai donc pas inclus mais il est donc possible d'acceder au page ci dessus sans connection grâce aux urls
 */


$app->run();


/*
class DemoMiddleware {

  public function __invoke($request, $response,$next) {
    $response->write("<h1>Bienvenue</h1>");
    return $next($request, $response);

  }
*/
 ?>
