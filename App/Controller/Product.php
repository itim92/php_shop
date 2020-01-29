<?php


namespace App\Controller;

use App\Model\Folder;
use App\Model\Product as ProductModel;
use App\Model\Model;
use App\Model\Vendor;
use App\Service\FolderService;
use App\Service\ProductService;
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

    public static function edit(int $product_id) {
        $product = new ProductModel();

        if ($product_id) {
            $product = ProductService::getById($product_id);
        }

        $vendors = VendorService::getList('id');
        $folders = FolderService::getList('id');

        smarty()->assign_by_ref('product', $product);
        smarty()->assign_by_ref('folders', $folders);
        smarty()->assign_by_ref('vendors', $vendors);
        smarty()->display('product/edit.tpl');
    }
}