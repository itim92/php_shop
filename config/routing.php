<?php


use App\Controller\Cart;
use App\Controller\Main;
use App\Controller\Vendor;

return [
    '/' => [Main::class, 'index'],
//    '/' => [Main::class, 'index2'],
    '/cart' => [Cart::class, 'view'],
    '/vendor/list' => [Vendor::class, 'list'],
    '/vendor/edit' => [Vendor::class, 'edit'],
];