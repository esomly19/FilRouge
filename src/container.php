<?php

$container = $app->getContainer();

$container['view'] = function ($container) {
  $dir = dirname(__DIR__); /* pour indiquer le fichier parent */
    $view = new \Slim\Views\Twig($dir . '/src/views', [
        'cache' => false /* 'path/to/cache' */
    ]);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
    $view->getEnvironment()->addGlobal('login',new App\controllers\loginController($container));

    return $view;
};


 ?>
