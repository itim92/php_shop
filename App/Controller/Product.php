<?php


namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Model\Product as ProductModel;
use App\Repository\FolderRepository;
use App\Repository\ProductRepository;
use App\Repository\VendorRepository;
use App\Service\CartService;
use App\Service\FolderService;
use App\Service\ProductService;
use App\Service\RequestService;
use App\Service\VendorService;

class Product extends AbstractController
{

    public static function buy() {
        $product_id = RequestService::getIntFromGet('product_id', 0);
        $product = ProductService::getById($product_id);

        CartService::addProduct($product);

        RequestService::redirect($_SERVER['HTTP_REFERER']);
    }

    public function list(Request $request, ProductRepository $productRepository, FolderRepository $folderRepository, VendorRepository $vendorRepository) {

        $current_page = $request->getIntFromGet('page', 1);
        $per_page = 30;
        $start = $per_page * ($current_page - 1);

        $products = [
            'count' => $productRepository->getCount(),
            'items' => $productRepository->findAllWithLimit($per_page, $start),
        ];
        $vendors = $vendorRepository->findAll();
        $folders = $folderRepository->findAll();

        $paginator = [
            'pages' => ceil($products['count'] / $per_page),
            'current' => $current_page
        ];

//        return new Response();

        return $this->json([
            'hello' => 'world',
        ]);
        

        return $this->render('index.tpl', [
           'products' => $products,
           'vendors' => $vendors,
           'folders' => $folders,
           'paginator' => $paginator,
        ]);
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