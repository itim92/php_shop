<?php


namespace App\Service;


use App\Model\Cart;
use App\Model\Product;

class CartService
{
    /**
     * @var string
     */
    private static $session_key = 'shop_cart';

    /**
     * @var Cart
     */
    private static $cart;

    public static function getCart() {
        echo "<pre>"; var_dump(static::isCartExist()); echo "</pre>";
        echo "<pre>"; var_dump($_SESSION); echo "</pre>";
        
        if (static::isCartExist()) {
            $cart_data = $_SESSION[static::$session_key];

            static::$cart = unserialize($cart_data);
        }

        if (!(static::$cart instanceof Cart)) {

            static::$cart = new Cart();
        }

        return static::$cart;
    }

    public static function storeCart() {
        $serialized_cart = serialize(static::getCart());
        
        $_SESSION[static::$session_key] = $serialized_cart;
    }

    public static function addProduct(Product $product) {
        $cart = static::getCart();
        $cart->add($product);

        static::storeCart();
    }

    private static function isCartExist() {
        return isset($_SESSION[static::$session_key]);
    }
}