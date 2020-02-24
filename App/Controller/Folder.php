<?php


namespace App\Controller;


use App\Http\Response;
use App\MySQL\Exceptions\GivenClassNotImplementerITableRowException;
use App\MySQL\Exceptions\QueryException;
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
        $folder_id = (int) $this->getRoute()->getParam('folder_id');

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
        $folder_id = (int) $this->request->getIntFromPost('folder_id');
        $name = $this->request->getStringFromPost('name');

        if (!$name) {
            die('Name required');
        }

        $folder = $folderRepository->findOrCreate($folder_id);
        $folder->setName($name);

        $folderRepository->save($folder);

        return $this->redirectToList();
    }

    /**
     * @param FolderRepository $repository
     * @return Response
     *
     * @Route(url="/folder/delete")
     *
     * @throws GivenClassNotImplementerITableRowException
     * @throws QueryException
     */
    public function delete(FolderRepository $repository) {
        $folder_id = (int) $this->request->getIntFromPost('folder_id');

        $folder = $repository->find($folder_id);
        $repository->delete($folder);

        return $this->redirectToList();
    }

    private function redirectToList() {
        return $this->redirect('/folder/list');
    }
}