<?php


namespace App\Controller;

use App\Model\Product as ProductModel;
use App\Service\CartService;
use App\Service\FolderService;
use App\Service\ProductService;
use App\Service\RequestService;
use App\Service\VendorService;

class Product
{
    private function __construct()
    {
    }

    public static function buy() {
        $product_id = RequestService::getIntFromGet('product_id', 0);
        $product = ProductService::getById($product_id);

        CartService::addProduct($product);

        RequestService::redirect($_SERVER['HTTP_REFERER']);
    }

    public static function list(RequestService $requestService) {

        $current_page = RequestService::getIntFromGet('page', 1);
        $per_page = 30;
        $start = $per_page * ($current_page - 1);

        $products = [
            'count' => ProductService::getCount(),
            'items' => ProductService::getList('id', $start, $per_page),
        ];
        $vendors = VendorService::getList('id');
        $folders = FolderService::getList('id');

        $paginator = [
            'pages' => ceil($products['count'] / $per_page),
            'current' => $current_page
        ];


        smarty()->assign_by_ref('products', $products);
        smarty()->assign_by_ref('vendors', $vendors);
        smarty()->assign_by_ref('folders', $folders);
        smarty()->assign_by_ref('paginator', $paginator);
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