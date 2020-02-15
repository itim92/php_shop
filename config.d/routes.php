<?php


use App\Controller\Cart;
use App\Controller\Main;
use App\Controller\Product;
use App\Controller\Vendor;

return [
    '/' => [Product::class, 'list'],
    '/cart' => [Cart::class, 'view'],
    '/vendor/list' => [Vendor::class, 'list'],
    '/vendor/edit' => [Vendor::class, 'edit'],
];