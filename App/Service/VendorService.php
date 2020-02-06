<?php


namespace App\Service;


use App\Model\Vendor;

class VendorService
{

    private static $model = Vendor::class;

    private function __construct()
    {
    }

    public static function save(Vendor $vendor) {
        $vendor_id = $vendor->getId();

        if ($vendor_id > 0) {
            $vendor = static::edit($vendor);
        } else {
            $vendor = static::create($vendor);
        }

        return $vendor;
    }

    private static function edit(Vendor $vendor)
    {
        $vendor_id = $vendor->getId();

        if ($vendor_id < 1) {
            $message = 'Its only edit vendor, not create';
            throw new \Exception($message);
        }

        db()->update('vendors', [
            'name' => db()->escape($vendor->getName())
        ], [
            'id' => $vendor_id
        ]);

        return static::getById($vendor_id);
    }

    private static function create(Vendor $vendor) {
        $vendor_id = db()->insert('vendors', [
            'name' => db()->escape($vendor->getName())
        ]);

        return static::getById($vendor_id);
    }

    /**
     * @param int $vendor_id
     * @return Vendor|null
     */
    public static function getById(int $vendor_id) {
        $query = "SELECT * FROM vendors WHERE id = $vendor_id";
        return db()->fetchRow($query, static::$model);
    }

    /**
     * @return Vendor|null
     */
    public static function getRandom() {
        $query = "SELECT * FROM vendors ORDER BY RAND() LIMIT 1";
        return db()->fetchRow($query, static::$model);
    }

    /**
     * @param string|null $hash_key
     * @return Vendor[]
     */
    public static function getList(string $hash_key = null): array {
        $query = "SELECT * FROM vendors";

        if (is_null($hash_key)) {
            $vendors = db()->fetchAll($query, static::$model);
        } else {
            $vendors = db()->fetchAllHash($query, $hash_key, static::$model);
        }

        return $vendors;
    }

    public static function delete(Vendor $vendor) {
        $vendor_id = $vendor->getId();

        db()->delete('vendors', [
            'id' => $vendor_id,
        ]);

        return true;
    }
}