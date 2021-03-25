<?php

use Lib\router\Request;
use Lib\router\Router;
use Lib\services\SingletonServiceCreator;

/** @var Router $router */
$router = SingletonServiceCreator::get(Router::class);

$Auth = function (Request $request, array $routeValues) {
    if (isset($_COOKIE['auth_session_id'])) {
        session_id($_COOKIE['auth_session_id']);
        session_start();
    } else {
        Router::redirect('/login');
    }
};

$router->get(
    '/',
    [
        function (Request $request, array $routeValues) {
            Router::redirect('/login');
        }
    ]
);

$router->get(
    '/login',
    [
        function (Request $request, array $routeValues) {
            $data = Router::getRedirectedData();
            if ($data) {
                extract($data);
            }
            include('views/login.php');
        }

    ]
);

$router->get(
    '/register',
    [
        function (Request $request, array $routeValues) {
            $data = Router::getRedirectedData();
            if ($data) {
                extract($data);
            }
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
                Router::redirect('/login', compact('email', 'error'));
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

                    Router::redirect('/home');
                } else {
                    $error = 'Invalid Password';
                    Router::redirect('/login', compact('email', 'error'));
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
            $email = $request->inputs['POST']['email'];

            $password = $request->inputs['POST']['password'];
            if (strlen($password) < 8) {
                $error = 'Password must be at least 8 characters';
                Router::redirect('/register', compact('name', 'email', 'error'));
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                if (User::query()->select()->where('email', $email)->get() == false) {
                    $user = new User;
                    $user->name = $name;
                    $user->password = $password_hash;
                    $user->email = $email;
                    $user->create();

                    session_start();
                    $arr_cookie_options = array(
                        'expires' => time() + 86400,
                        'secure' => false,     // or false
                        'httponly' => true,    // or false
                    );
                    setcookie('auth_session_id', session_id(), $arr_cookie_options);
                    $user = User::query()->select()->where('email', $email)->getFirstOrFalse();
                    $_SESSION['auth_user'] = $user;
//
                    //Make a new database entry for user's root folder.
                    $folder = new Folder;
                    $folder->name = 'root';
                    $folder->user_id = $user['id'];
                    $folder->no_of_items = 0;
                    $folder->parent_folder_id = 0;
                    $folder->size = 0;
                    $folder->create();

                    Router::redirect('/home');
                } else {
                    $error = 'Email already registered';
                    Router::redirect('/register', compact('name', 'email', 'error'));
                }
            }
        }
    ]
);



$router->get(
    '/home',
    [
        $Auth,
        function (Request $request, array $routeValues) {
            include('views/home.php');
        }
    ]
);

$router->get(
    '/test',
    [
        function (Request $request, array $routeValues) {
            Folder::drop();
            Folder::createTable();

            File::drop();
            File::createTable();
        }

    ]
);

$router->post(
    '/logout',
    [
        function (Request $request, array $routeValues) {
            if (isset($_COOKIE['auth_session_id'])) {
                setcookie("auth_session_id", "", time() - 3600);
                unset($_COOKIE['auth_session_id']);
            }
            Router::redirect('/login');
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
