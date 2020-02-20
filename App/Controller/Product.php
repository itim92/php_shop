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

    /**
     * @param ProductRepository $productRepository
     * @param CartService $cartService
     *
     * @Route(url="/product/buy")
     *
     * @return Response
     */
    public function buy(ProductRepository $productRepository, CartService $cartService) {
        $product_id = $this->request->getIntFromGet('product_id');

        $product = $productRepository->find($product_id);

        $cartService->addProduct($product);

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * @param Request $request
     * @param ProductRepository $productRepository
     * @param FolderRepository $folderRepository
     * @param VendorRepository $vendorRepository
     *
     * @Route(url="/")
     * @Route(url="/product/list")
     *
     * @return Response
     */
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


        return $this->render('index.tpl', [
           'products' => $products,
           'vendors' => $vendors,
           'folders' => $folders,
           'paginator' => $paginator,
        ]);
    }

    /**
     * @param ProductRepository $productRepository
     * @param VendorRepository $vendorRepository
     * @param FolderRepository $folderRepository
     *
     * @Route(url="/product/view")
     *
     * @return Response
     */
    public function view(ProductRepository $productRepository, VendorRepository $vendorRepository, FolderRepository $folderRepository)
    {
        $product_id = $this->request->getIntFromGet('product_id');

        $product = $productRepository->find($product_id);

        $vendors = $vendorRepository->findAll();
        $folders = $folderRepository->findAll();

        return $this->render('product/view.tpl', [
            'product' => $product,
            'folders' => $folders,
            'vendors' => $vendors,
        ]);
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