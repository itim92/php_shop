<?php


namespace App\Service;


use App\Model\Folder;

class FolderService
{
    private $model = Folder::class;

    public function __construct()
    {
    }

    public function save(Folder $folder) {
        $folder_id = $folder->getId();

        if ($folder_id > 0) {
            $folder = $this->edit($folder);
        } else {
            $folder = $this->create($folder);
        }

        return $folder;
    }

    private function edit(Folder $folder)
    {
        $folder_id = $folder->getId();

        if ($folder_id < 1) {
            $message = 'Its only edit folder, not create';
            throw new \Exception($message);
        }

        db()->update('folders', [
            'name' => db()->escape($folder->getName())
        ], [
            'id' => $folder_id
        ]);

        return $this->getById($folder_id);
    }

    private function create(Folder $folder) {
        $folder_id = db()->insert('folders', [
            'name' => db()->escape($folder->getName())
        ]);

        return $this->getById($folder_id);
    }

    /**
     * @param int $folder_id
     * @return Folder|null
     */
    public function getById(int $folder_id) {
        $query = "SELECT * FROM folders WHERE id = $folder_id";
        return db()->fetchRow($query, $this->model);
    }

    /**
     * @return Folder|null
     */
    public function getRandom() {
        $query = "SELECT * FROM folders ORDER BY RAND() LIMIT 1";
        return db()->fetchRow($query, $this->model);
    }

    /**
     * @param string|null $hash_key
     * @return Folder[]
     */
    public function getList(string $hash_key = null): array {
        $query = "SELECT * FROM folders";

        if (is_null($hash_key)) {
            $folders = db()->fetchAll($query, $this->model);
        } else {
            $folders = db()->fetchAllHash($query, $hash_key, $this->model);
        }

        return $folders;
    }

    public function delete(Folder $folder) {
        $folder_id = $folder->getId();

        db()->delete('folders', [
            'id' => $folder_id,
        ]);

        return true;
    }
}