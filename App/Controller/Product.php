<?php


namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Repository\FolderRepository;
use App\Repository\ProductRepository;
use App\Repository\VendorRepository;
use App\Service\CartService;
use App\Service\UserService;

class Product extends AbstractController
{

    /**
     * @param ProductRepository $productRepository
     * @param CartService $cartService
     *
     * @Route(url="/product/buy/{product_id}")
     *
     * @return Response
     */
    public function buy(ProductRepository $productRepository, CartService $cartService) {
        $product_id = $this->getRoute()->getParam('product_id');

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
     * @Route(url="/product/view/{product_id}")
     *
     * @return Response
     */
    public function view(ProductRepository $productRepository, VendorRepository $vendorRepository, FolderRepository $folderRepository)
    {
        $product_id = $this->getRoute()->getParam('product_id');

        $product = $productRepository->find($product_id);

        $vendors = $vendorRepository->findAll();
        $folders = $folderRepository->findAll();

        return $this->render('product/view.tpl', [
            'product' => $product,
            'folders' => $folders,
            'vendors' => $vendors,
        ]);
    }

    /**
     * @param UserService $userService
     * @param ProductRepository $productRepository
     * @param VendorRepository $vendorRepository
     * @param FolderRepository $folderRepository
     *
     * @Route(url="/product/edit")
     * @Route(url="/product/edit/{product_id}")
     *
     * @return Response
     */
    public function edit(UserService $userService, ProductRepository $productRepository, VendorRepository $vendorRepository, FolderRepository $folderRepository) {
        $user = $userService->getCurrentUser();

        if (!$user->getId()) {
            die('permission denied');
        }

        $product_id = (int) $this->getRoute()->getParam('product_id');

        if ($product_id) {
            $product = $productRepository->find($product_id);
        } else {
            $product = $productRepository->create();
        }

        $vendors = $vendorRepository->findAll();
        $folders = $folderRepository->findAll();

        $data = [
            'product' => $product,
            'folders' => $folders,
            'vendors' => $vendors,
        ];

        return $this->render('product/edit.tpl', $data);
    }


    /**
     * @param UserService $userService
     * @param ProductRepository $productRepository
     *
     * @Route(url="/product/editing")
     *
     * @return Response
     */
    public function editing(UserService $userService, ProductRepository $productRepository) {
        $user = $userService->getCurrentUser();

        if (!$user->getId()) {
            die('permission denied');
        }

        $product_id = $this->request->getStringFromPost('product_id');
        $name = $this->request->getStringFromPost('name');
        $price = $this->request->getFloatFromPost('price');
        $amount = $this->request->getIntFromPost('amount');
        $description = $this->request->getStringFromPost('description');
        $vendor_id = $this->request->getIntFromPost('vendor_id');
        $folder_ids = $this->request->getArrayFromPost('folder_ids');


        if (!$name || !$price || !$amount) {
            die('not enough data');
        }

        $product = $productRepository->findOrCreate($product_id);


        $product->setName($name);
        $product->setPrice($price);
        $product->setAmount($amount);
        $product->setDescription($description);
        $product->setVendorId($vendor_id);

        $product->removeAllFolders();

        foreach ($folder_ids as $folder_id) {
            $product->addFolderId($folder_id);
        }

        $productRepository->save($product);

        return $this->redirect('/');
    }
}