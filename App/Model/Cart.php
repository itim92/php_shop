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

    public function getAmount(): int {
        $amount = 0;

        foreach ($this->getItems() as $item) {
            $amount+= $item->getAmount();
        }

        return $amount;
    }

    public function getPrice(): float {
        $price = 0;

        foreach ($this->getItems() as $item) {
            $price+= $item->getPrice();
        }

        return $price;
    }

    public function add(Product $product) {

        $cart_item = $this->getItem($product);
        $cart_item->incrementAmount();
        $this->addCartItem($cart_item);
    }


    public function remove(Product $product) {

    }

    public function getItemsCount() {
        return count($this->getItems());
    }

    public function getItems() {
        return $this->cart_items;
    }

    private function getItem(Product $product) {
        $product_id = $product->getId();

        return $this->cart_items[$product_id] ?? new CartItem($product);
    }



//    public function isProductInCart(Product $product) {
//        $product_id = $product->getId();
//
//        return array_key_exists($product_id, $this->getItems());
//    }

    private function addCartItem(CartItem $cart_item) {
        $product_id = $cart_item->getProduct()->getId();

        $this->cart_items[$product_id] = $cart_item;
    }
}