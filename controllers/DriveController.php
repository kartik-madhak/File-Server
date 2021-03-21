<?php

use Lib\router\Request;
use Lib\router\Router;
use Lib\services\SingletonServiceCreator;

/** @var Router $router */
$router = SingletonServiceCreator::get(Router::class);

$Auth = function (Request $request, array $routeValues) use ($router) {
    if (isset($_COOKIE['auth_session_id'])) {
        session_id($_COOKIE['auth_session_id']);
        session_start();
    } else {
        Router::redirect('/login');
    }
};

/*
 * User : id, email, name, password
 * Folder:  id, user_id, parent_folder_id, name, size, no_of_items
 * File:    id, user_id, parent_folder_id, name, size, path = user1/folder1/abc.pdf
 *
 * User_1 -> (folder1 -> abc.pdf), (folder2 -> abc.pdf), xyz.pdf
 * User_2 -> ade.pdf,
 * */
$router->post(
    '/folder/{folderId}',
    [
        $Auth,
        function (Request $request, array $routeValues) {
            $folderId = $routeValues['folderId'];
            $mainFolder = Folder::query()->select()->where('parent_folder_id', $folderId)
                ->where('user_id', $_SESSION['auth_user']['id'])->getFirstOrFalse();
            if ($mainFolder == false) {
                echo json_encode(['msg' => 'Empty']);
            } else {
                $folders = Folder::query()->select()->where('parent_folder_id', $mainFolder['id'])
                    ->where('user_id', $_SESSION['auth_user']['id'])->get();
                $files = File::query()->select()->where('parent_folder_id', $mainFolder['id'])
                    ->where('user_id', $_SESSION['auth_user']['id'])->get();
                $_SESSION['auth_user_current_folder'] = $mainFolder['id'];
                echo json_encode(compact('folders', 'files'));
            }
        }

    ]
);

$router->post(
    '/folder-add',
    [
        $Auth,
        function (Request $request, array $routeValues) {
            $folderName = $request->inputs['POST']['folder_name'];
            $folder = new Folder;
            $folder->name = $folderName;
            $folder->size = 0;
            $folder->no_of_items = 0;
            $folder->user_id = $_SESSION['auth_user']['id'];
            $folder->parent_folder_id = $_SESSION['auth_user_current_folder'];
            $folder->create();
            echo json_encode(['msg'=> 'success']);
        }

    ]
);
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
