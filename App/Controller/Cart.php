<?php


namespace App\Controller;

use App\Model\Product as ProductModel;
use App\Service\CartService;
use App\Service\FolderService;
use App\Service\ProductService;
use App\Service\RequestService;
use App\Service\VendorService;

class Cart extends AbstractController
{

    /**
     * @Route(url="/cart/clear")
     */
    public function clear(CartService $cartService) {
        $cartService->clearCart();

        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

//    public static function view() {
//
//        smarty()->display('cart/view.tpl');
//    }
}