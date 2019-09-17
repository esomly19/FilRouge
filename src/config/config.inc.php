<?php
global $BASE_URL;

$config=array();

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db'] = [
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'database',
    'username' => '.$user.' ,
    'password' => '.$mdp.' ,
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
];

//connect to database
