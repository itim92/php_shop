<?php


namespace App\Controller;


use App\Service\RequestService;
use App\Service\VendorService;

class Vendor
{
    public static function list() {
        $vendors = VendorService::getList();

        smarty()->assign_by_ref('vendors', $vendors);
        smarty()->display('vendor/index.tpl');
    }

    public static function edit() {
        $vendor_id = RequestService::getIntFromGet('vendor_id', 0);

        $vendor = new \App\Model\Vendor();

        if ($vendor_id) {
            $vendor = VendorService::getById($vendor_id);
        }

        smarty()->assign_by_ref('vendor', $vendor);
        smarty()->display('vendor/edit.tpl');
    }




    public static function editing() {
        $vendor_id = RequestService::getIntFromPost('vendor_id');
        $name = RequestService::getStringFromPost('name');

        if (!$name) {
            die('Name required');
        }

        $vendor = new \App\Model\Vendor();
        if ($vendor_id) {
            $vendor = VendorService::getById($vendor_id);
        }

        $vendor->setName($name);

        VendorService::save($vendor);

        static::redirectToList();
    }

    public static function delete() {
        $vendor_id = RequestService::getIntFromPost('vendor_id');

        $vendor = VendorService::getById($vendor_id);
        VendorService::delete($vendor);

        static::redirectToList();
    }

    private static function redirectToList() {
        RequestService::redirect('/vendor/');
    }
}