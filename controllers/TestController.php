<?php

use Lib\router\Request;
use Lib\router\Router;
use Lib\services\SingletonServiceCreator;

/** @var Router $router */
$router = SingletonServiceCreator::get(Router::class);

$router->get(
    '/',
    [
        function (Request $request, array $routeValues) {
            include('views/login.php');
        }
        
    ]
);

$router->post(
    '/login',
    [
        function (Request $request, array $routeValues) {
            //include('views/home.php');
            var_dump($request);
        }
    ]
);
$router->get(
    '/temp',
    [
        function (Request $request, array $routeValues) {
            //User::createTable();
            $user= new User();
            $user->name="arrow";
            $user->password="123456";
            $user->email_id="abhijeet@gmail.com";
            $user->create();
        }
        
    ]
);
//
//$router->get(
//    '/home',
//    function (Request $request, array $routeValues) {
//        $inputsFromForms = $request->inputs;
//
//        if (isset($inputsFromForms['GET'])) {
//            include('views/home.php');
//        }
//    }
//);
//
//$router->post(
//    '/home',
//    function (Request $request, array $routeValues) {
//        $msg = 'POST REQUEST SUCCESSFUL';
//        include ('views/index.php');
//    }
//);
//
//$router->get(
//    '/testingAjax',
//    function (Request $request, array $routeValues) {
//        echo json_encode(['data' => 'IT SEEMS TO BE WORKING FINE!']);
//    }
//);
//
//$router->get(
//    '/migration',
//    function (Request $request, array $routeValues) {
//        include('views/migrationHandler.php');
//    }
//);
//
