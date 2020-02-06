<?php


namespace App\Controller;


use App\Service\FolderService;
use App\Service\RequestService;

class Folder
{
    public static function list() {
        $folders = FolderService::getList();

        smarty()->assign_by_ref('folders', $folders);
        smarty()->display('folder/index.tpl');
    }

    public static function edit() {
        $folder_id = RequestService::getIntFromGet('folder_id', 0);

        $folder = new \App\Model\Folder();

        if ($folder_id) {
            $folder = FolderService::getById($folder_id);
        }

        smarty()->assign_by_ref('folder', $folder);
        smarty()->display('folder/edit.tpl');
    }




    public static function editing() {
        $folder_id = RequestService::getIntFromPost('folder_id');
        $name = RequestService::getStringFromPost('name');

        if (!$name) {
            die('Name required');
        }

        $folder = new \App\Model\Folder();
        if ($folder_id) {
            $folder = FolderService::getById($folder_id);
        }

        $folder->setName($name);

        FolderService::save($folder);

        static::redirectToList();
    }

    public static function delete() {
        $folder_id = RequestService::getIntFromPost('folder_id');

        $folder = FolderService::getById($folder_id);
        FolderService::delete($folder);

        static::redirectToList();
    }

    private static function redirectToList() {
        RequestService::redirect('/folder/');
    }
}