<?php


namespace App\Controller;


use App\Http\Response;
use App\Repository\FolderRepository;
use App\Service\FolderService;
use App\Service\RequestService;

class Folder extends AbstractController
{

    /**
     * @param FolderRepository $folderRepository
     *
     * @Route(url="/folder/list")
     *
     * @return Response
     */
    public function list(FolderRepository $folderRepository) {
        $folders = $folderRepository->findAll();

        return $this->render('folder/index.tpl', [
            'folders' => $folders,
        ]);
//
//        smarty()->assign_by_ref('folders', $folders);
//        smarty()->display('folder/index.tpl');
    }


    /**
     * @param FolderRepository $folderRepository
     *
     * @Route(url="/folder/edit")
     * @Route(url="/folder/edit/{folder_id})
     *
     * @return Response
     */
    public function edit(FolderRepository $folderRepository) {
        $folder_id = $this->getRoute()->getParam('folder_id');

        $folder = $folderRepository->findOrCreate($folder_id);

        return $this->render('folder/edit.tpl', [
            'folder' => $folder
        ]);
    }

    /**
     * @param FolderRepository $folderRepository
     *
     * @Route(url="/folder/editing")
     *
     * @return Response
     */
    public function editing(FolderRepository $folderRepository) {
        $folder_id = $this->request->getIntFromPost('folder_id');
        $name = $this->request->getStringFromPost('name');

        if (!$name) {
            die('Name required');
        }

        $folder = $folderRepository->findOrCreate($folder_id);
        $folder->setName($name);

        $folderRepository->save($folder);

        return $this->redirectToList();
    }

    public function delete() {
        $folder_id = RequestService::getIntFromPost('folder_id');

        $folder = FolderService::getById($folder_id);
        FolderService::delete($folder);

        return $this->redirectToList();
    }

    private function redirectToList() {
        return $this->redirect('/folder/list');
    }
}