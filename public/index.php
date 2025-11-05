<?php

//ini_set('error_reporting', 'E_STRICT');
require __DIR__ . '/../vendor/autoload.php';
// $route = new \App\Route;
use App\Config;
use App\Core\Route;

Config::init();
$route = new Route();
$route->get('/', 'HomeController', 'index');
$route->post('/convert', 'HomeController', 'convert');

// Executa o roteamento
$route->run();


// echo '<pre>';
// print_r($router->getUrl());
// echo '</pre>';
