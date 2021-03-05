<?php

use Lib\router\Request;
use Lib\router\Router;
use Lib\services\SingletonServiceCreator;

/** @var Router $router */
$router = SingletonServiceCreator::get(Router::class);

$router->add(
    '/',
    function (Request $request, array $routeValues) {
        include('views/index.php');
    }
);

$router->add(
    '/home',
    function (Request $request, array $routeValues) {
        $inputsFromForms = $request->inputs;

        if (isset($inputsFromForms['GET'])) {
            include ('views/home.php');
        }
    }
);

$router->add(
    '/migration',
    function (Request $request, array $routeValues) {
        include('views/migrationHandler.php');
    }
);

