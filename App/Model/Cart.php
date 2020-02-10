<?php


namespace App\Model;


class Cart
{

    /**
     * @var int
     */
    private $amount = 0;

    /**
     * @var float
     */
    private $price = 0;

    /**
     * @var CartItem[]
     */
    private $cart_items = [];

    public function add(Product $product) {
        $cart_item = new CartItem($product);
        $cart_item->setAmount(1);

        $this->addCartItem($cart_item);
    }


    public function remove(Product $product) {

    }

    public function getProductIds(): array {

    }

    public function isProductInCart(Product $product) {
    }

    private function addCartItem(CartItem $cart_item) {
        $this->cart_items[] = $cart_item;
    }
}