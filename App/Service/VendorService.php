<?php


namespace App\Service;


use App\Model\Vendor;

class VendorService
{

    private $model = Vendor::class;

    public function __construct()
    {
    }

    public function save(Vendor $vendor) {
        $vendor_id = $vendor->getId();

        if ($vendor_id > 0) {
            $vendor = $this->edit($vendor);
        } else {
            $vendor = $this->create($vendor);
        }

        return $vendor;
    }

    private function edit(Vendor $vendor)
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

        return $this->getById($vendor_id);
    }

    private function create(Vendor $vendor) {
        $vendor_id = db()->insert('vendors', [
            'name' => db()->escape($vendor->getName())
        ]);

        return $this->getById($vendor_id);
    }

    /**
     * @param int $vendor_id
     * @return Vendor|null
     */
    public function getById(int $vendor_id) {
        $query = "SELECT * FROM vendors WHERE id = $vendor_id";
        return db()->fetchRow($query, $this->model);
    }

    /**
     * @return Vendor|null
     */
    public function getRandom() {
        $query = "SELECT * FROM vendors ORDER BY RAND() LIMIT 1";
        return db()->fetchRow($query, $this->model);
    }

    /**
     * @param string|null $hash_key
     * @return Vendor[]
     */
    public function getList(string $hash_key = null): array {
        $query = "SELECT * FROM vendors";

        if (is_null($hash_key)) {
            $vendors = db()->fetchAll($query, $this->model);
        } else {
            $vendors = db()->fetchAllHash($query, $hash_key, $this->model);
        }

        return $vendors;
    }

    public function delete(Vendor $vendor) {
        $vendor_id = $vendor->getId();

        db()->delete('vendors', [
            'id' => $vendor_id,
        ]);

        return true;
    }
}