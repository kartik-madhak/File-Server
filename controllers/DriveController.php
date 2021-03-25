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
            if ($folderId == 0) {
                $mainFolder = Folder::query()->select()->where('parent_folder_id', $folderId)
                    ->where('user_id', $_SESSION['auth_user']['id'])->getFirstOrFalse();
            } else {
                $mainFolder = Folder::query()->select()->where('id', $folderId)
                    ->where('user_id', $_SESSION['auth_user']['id'])->getFirstOrFalse();
            }
            $folders = Folder::query()->select()->where('parent_folder_id', $mainFolder['id'])
                ->where('user_id', $_SESSION['auth_user']['id'])->get();
            $files = File::query()->select()->where('parent_folder_id', $mainFolder['id'])
                ->where('user_id', $_SESSION['auth_user']['id'])->get();
            $_SESSION['auth_user_current_folder'] = $mainFolder['id'];
            echo json_encode(compact('folders', 'files', 'mainFolder'));
        }

    ]
);

$router->post(
    '/folder-add',
    [
        $Auth,
        function (Request $request, array $routeValues) {
//             echo json_encode(compact('request'));
            $folderName = $request->inputs['POST']['folder_name'];
            $folder = new Folder;
            $folder->name = $folderName;
            $folder->size = 0;
            $folder->no_of_items = 0;
            $folder->user_id = $_SESSION['auth_user']['id'];
            $folder->parent_folder_id = $_SESSION['auth_user_current_folder'];
            $folder->create();
            $res = Folder::query()->select()->where('name', $folderName)->where(
                'parent_folder_id',
                $_SESSION['auth_user_current_folder']
            )->where('user_id', $_SESSION['auth_user']['id'])->getFirstOrFalse();

            $current_folder_id = $_SESSION['auth_user_current_folder'];
            do {
                /** @var Folder $folder */
                $folder = (object)Folder::query()->select()->where('id', $current_folder_id)->getFirstOrFalse();

                $newFolder = new Folder;
                $newFolder->id = $folder->id;
                $newFolder->name = $folder->name;
                $newFolder->no_of_items = $folder->no_of_items;
                $newFolder->parent_folder_id = $folder->parent_folder_id;
                $newFolder->size = $folder->size;
                $newFolder->user_id = $folder->user_id;
                $newFolder->created_at = $folder->created_at;
                $newFolder->updated_at = $folder->updated_at;

                $newFolder->no_of_items += 1;
                $newFolder->save();
                $current_folder_id = $newFolder->parent_folder_id;
            } while ($current_folder_id !== 0);
            $mainFolder = Folder::query()->select()->where('id', $_SESSION['auth_user_current_folder'])
                ->where('user_id', $_SESSION['auth_user']['id'])->getFirstOrFalse();

            echo json_encode(['msg' => 'success', 'folder' => $res, 'mainFolder' => $mainFolder]);
        }

    ]
);

$router->post(
    '/files-add',
    [
        $Auth,
        function (Request $request, array $routeValues) {
            $files = $request->inputs['FILES'];
//            if (isset($files['userfile'])) {
//                $files = $files['userfile'];
//                echo json_encode(['files' => $files]);
            $res = [];
            foreach ($files as $k => $file) {
                //random filename
                $fileName = substr(str_shuffle(md5(time())), 0, 10) . '.' . $file['name'];;
                $filePath = 'storage/' . $fileName;
                if (File::query()->select()
                    ->where('parent_folder_id', $_SESSION['auth_user_current_folder'])
                    ->where('name', $file['name'])
                    ->where('user_id', $_SESSION['auth_user']['id'])
                    ->getFirstOrFalse()

                ) {
                    echo json_encode(
                        [
                            'msg' => 'FAILED',
                            'error' => 'One or more files have the same name as one of the existing files.'
                        ]
                    );
                    return;
                }

                if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                    echo json_encode(['msg' => 'FAILED', 'file' => $file, 'error' => 'Failed to upload to the server']);
                    return;
                }

                $newFile = new File;
                $newFile->name = $file['name'];
                $newFile->size = $file['size'];
                $newFile->path = $filePath;
                $newFile->parent_folder_id = $_SESSION['auth_user_current_folder'];
                $newFile->user_id = $_SESSION['auth_user']['id'];
                $newFile->create();

                $current_folder_id = $_SESSION['auth_user_current_folder'];
                do {
                    /** @var Folder $folder */
                    $folder = (object)Folder::query()->select()->where('id', $current_folder_id)->getFirstOrFalse();

                    $newFolder = new Folder;
                    $newFolder->id = $folder->id;
                    $newFolder->name = $folder->name;
                    $newFolder->no_of_items = $folder->no_of_items;
                    $newFolder->parent_folder_id = $folder->parent_folder_id;
                    $newFolder->size = $folder->size;
                    $newFolder->user_id = $folder->user_id;
                    $newFolder->created_at = $folder->created_at;
                    $newFolder->updated_at = $folder->updated_at;

                    $newFolder->size += $newFile->size;
                    $newFolder->no_of_items += 1;
                    $newFolder->save();
                    $current_folder_id = $newFolder->parent_folder_id;
                } while ($current_folder_id !== 0);

                $res[] = File::query()->select()->where('path', $filePath)->where(
                    'parent_folder_id',
                    $_SESSION['auth_user_current_folder']
                )->where('user_id', $_SESSION['auth_user']['id'])->getFirstOrFalse();
            }
            $files = $res;
            $mainFolder = Folder::query()->select()->where('id', $_SESSION['auth_user_current_folder'])
                ->where('user_id', $_SESSION['auth_user']['id'])->getFirstOrFalse();
            $msg = 'SUCCESS';
            echo json_encode(compact('files', 'mainFolder'));
        }
    ]
);

$router->get(
    '/download/file/{fileId}',
    [
        $Auth,
        function (Request $request, array $routeValues) {
            $fileId = $routeValues['fileId'];
            $file = File::query()->select()->where('id', $fileId)
                ->where('user_id', $_SESSION['auth_user']['id'])->getFirstOrFalse();

            if (!$file) {
                $msg = 'ERROR OCCURRED';
                echo json_encode(compact('msg'));
            } else {
                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=" . $file['name'] . "");
                header("Content-Transfer-Encoding: binary");
                header("Content-Type: binary/octet-stream");
                readfile($file['path']);
            }
        }
    ]
);

function recursiveAssignFolderStructure(array &$folderStructure, int $id, string $name)
{
    $folders = Folder::query()->select()
        ->where('user_id', $_SESSION['auth_user']['id'])
        ->where('parent_folder_id', $id)->get();

    if (!isset($folderStructure[$id])) {
        $folderStructure[$id]['name'] = $name;
    }


    for ($i = 0; $i < count($folders); $i++) {
        recursiveAssignFolderStructure($folderStructure[$id], $folders[$i]['id'], $folders[$i]['name']);
    }
}

$router->get(
    '/folders/structure',
    [
        $Auth,
        function (Request $request, array $routeValues) {
            $mainFolder = Folder::query()->select()
                ->where('user_id', $_SESSION['auth_user']['id'])
                ->where('parent_folder_id', 0)->getFirstOrFalse();
            $folderStructure = [];
            recursiveAssignFolderStructure($folderStructure, $mainFolder['id'], $mainFolder['name']);
            echo json_encode(compact('folderStructure'));
//            dd($folderStructure);
        }
    ]
);

$router->post(
    '/rename',
    [
        $Auth,
        function (Request $request, array $routeValues) {
            $type = $request->inputs['POST']['type'];
            $id = $request->inputs['POST']['id'];
            $newName = $request->inputs['POST']['name'];
            if ($type === 'file') {
                $file = new File;
                $file->id = $id;
                $file->name = $newName;
                $file->save();
            } else {
                $folder = new Folder;
                $folder->id = $id;
                $folder->name = $newName;
                $folder->save();
            }
            echo json_encode(['msg' => 'success']);
        }
    ]
);

$router->get(
    '/download/folder/{folderId}',
    [
        $Auth,
        function (Request $request, array $routeValues) {
            $folderId = $routeValues['folderId'];

            $folder = Folder::query()->select()->where('id', $folderId)
                ->where('user_id', $_SESSION['auth_user']['id'])->getFirstOrFalse();

            if (!$folder) {
                $msg = 'ERROR OCCURRED';
            } else {
                $zip = new ZipArchive;
                $folderName = $folder['name'];
                $zip->open($folderName . '.zip', ZipArchive::CREATE);
                $files = File::query()->select()->where('user_id', $_SESSION['auth_user']['id'])
                    ->where('parent_folder_id', $folderId)
                    ->get();
                foreach ($files as $file) {
                    $zip->addFile($file['path']);
                }
                $download = $zip->filename;
                $zip->close();

                header("Cache-Control: public");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment; filename=" . $folderName . ".zip");
                header("Content-Transfer-Encoding: binary");
                header("Content-Type: binary/octet-stream");
                header('Content-Length: ' . filesize($download));
                readfile($download);
            }
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
