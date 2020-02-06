<?php


namespace App\Service;


use App\Model\Folder;

class FolderService
{
    private static $model = Folder::class;

    private function __construct()
    {
    }

    public static function save(Folder $folder) {
        $folder_id = $folder->getId();

        if ($folder_id > 0) {
            $folder = static::edit($folder);
        } else {
            $folder = static::create($folder);
        }

        return $folder;
    }

    private static function edit(Folder $folder)
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

        return static::getById($folder_id);
    }

    private static function create(Folder $folder) {
        $folder_id = db()->insert('folders', [
            'name' => db()->escape($folder->getName())
        ]);

        return static::getById($folder_id);
    }

    /**
     * @param int $folder_id
     * @return Folder|null
     */
    public static function getById(int $folder_id) {
        $query = "SELECT * FROM folders WHERE id = $folder_id";
        return db()->fetchRow($query, static::$model);
    }

    /**
     * @return Folder|null
     */
    public static function getRandom() {
        $query = "SELECT * FROM folders ORDER BY RAND() LIMIT 1";
        return db()->fetchRow($query, static::$model);
    }

    /**
     * @param string|null $hash_key
     * @return Folder[]
     */
    public static function getList(string $hash_key = null): array {
        $query = "SELECT * FROM folders";

        if (is_null($hash_key)) {
            $folders = db()->fetchAll($query, static::$model);
        } else {
            $folders = db()->fetchAllHash($query, $hash_key, static::$model);
        }

        return $folders;
    }

    public static function delete(Folder $folder) {
        $folder_id = $folder->getId();

        db()->delete('folders', [
            'id' => $folder_id,
        ]);

        return true;
    }
}