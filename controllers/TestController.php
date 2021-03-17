<?php

use Lib\router\Request;
use Lib\router\Router;
use Lib\services\SingletonServiceCreator;

/** @var Router $router */
$router = SingletonServiceCreator::get(Router::class);

$Auth = function (Request $request, array $routeValues) {
    session_id($_COOKIE['auth_session_id'];
};

$Home = function (Request $request, array $routeValues) {
//    include()
//    TODO
};

$router->get(
    '/',
    [
        function (Request $request, array $routeValues) {
            include('views/login.php');
        }

    ]
);

$router->get(
    '/register',
    [
        function (Request $request, array $routeValues) {
            include('views/register.php');
        }

    ]
);

$router->post(
    '/login',
    [
        function (Request $request, array $routeValues) {
            $email = $request->inputs['POST']['email'];
            $password = $request->inputs['POST']['password'];

            $user = User::query()->select()->where('email', $email)->getFirstOrFalse();
            if ($user == false) {
                $error = 'Email not registered';
                include('views/login.php');
            } else {
                if (password_verify($password, $user['password'])) {
                    session_start();
                    $arr_cookie_options = array(
                        'expires' => time() + 86400,
                        'secure' => false,     // or false
                        'httponly' => true,    // or false
                    );
                    setcookie('auth_session_id', session_id(), $arr_cookie_options);
                    $_SESSION['auth_user'] = $user;
                    // TODO
                } else {
                    $error = 'Invalid Password';
                    include('views/login.php');
                }
            }
        }
    ]
);

$router->post(
    '/register',
    [
        function (Request $request, array $routeValues) {
            $name = $request->inputs['POST']['name'];

            $password = $request->inputs['POST']['password'];
            if (strlen($password) < 8) {
                $error = 'Password must be at least 8 characters';
                include('views/register.php');
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $email = $request->inputs['POST']['email'];

                if (User::query()->select()->where('email', $email)->get() == false) {
                    $user = new User;
                    $user->name = $name;
                    $user->password = $password_hash;
                    $user->email = $email;
                    $user->create();

                    // TODO

                } else {
                    $error = 'Email already registered';
                    include('views/register.php');
                }
            }
        }
    ]
);


$router->get(
    '/home',
    [
        $Home,
    ]
);

$router->get(
    '/test',
    [
        function (Request $request, array $routeValues) {
            User::drop();
            User::createTable();
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
