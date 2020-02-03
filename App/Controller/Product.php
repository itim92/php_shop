<?php


namespace App\Controller;

use App\Model\Product as ProductModel;
use App\Service\FolderService;
use App\Service\ProductService;
use App\Service\RequestService;
use App\Service\VendorService;

class Product
{
    private function __construct()
    {
    }

    public static function list() {
        $products = ProductService::getList('id');
        $vendors = VendorService::getList('id');
        $folders = FolderService::getList('id');

        smarty()->assign_by_ref('products', $products);
        smarty()->assign_by_ref('vendors', $vendors);
        smarty()->assign_by_ref('folders', $folders);
        smarty()->display('index.tpl');
    }

    public static function view() {
        $product_id = RequestService::getIntFromGet('product_id');

        $product = ProductService::getById($product_id);

        $vendors = VendorService::getList('id');
        $folders = FolderService::getList('id');

        smarty()->assign_by_ref('product', $product);
        smarty()->assign_by_ref('folders', $folders);
        smarty()->assign_by_ref('vendors', $vendors);
        smarty()->display('product/view.tpl');
    }

    public static function edit() {
        $user = user();

        if (!$user->getId()) {
            die('permission denied');
        }


        $product_id = RequestService::getIntFromGet('product_id');

        if ($product_id) {
            $product = ProductService::getById($product_id);
        } else {
            $product = new ProductModel();
        }

        $vendors = VendorService::getList('id');
        $folders = FolderService::getList('id');

        smarty()->assign_by_ref('product', $product);
        smarty()->assign_by_ref('folders', $folders);
        smarty()->assign_by_ref('vendors', $vendors);
        smarty()->display('product/edit.tpl');
    }

    public static function editing() {
        $user = user();


        if (!$user->getId()) {
            die('permission denied');
        }


        $product_id = RequestService::getIntFromPost('product_id');
        $name = RequestService::getStringFromPost('name');
        $price = RequestService::getFloatFromPost('price');
        $amount = RequestService::getIntFromPost('amount');
        $description = RequestService::getStringFromPost('description');
        $vendor_id = RequestService::getIntFromPost('vendor_id');
        $folder_ids = RequestService::getArrayFromPost('folder_ids');


        if (!$name || !$price || !$amount) {
            die('not enough data');
        }

        $product = new Product();

        if ($product_id) {
            $product = ProductService::getById($product_id);
        }

        $product->setName($name);
        $product->setPrice($price);
        $product->setAmount($amount);
        $product->setDescription($description);
        $product->setVendorId($vendor_id);

        $product->removeAllFolders();
        foreach ($folder_ids as $folder_id) {
            $product->addFolderId($folder_id);
        }

        ProductService::save($product);

        RequestService::redirect('/');
    }
}