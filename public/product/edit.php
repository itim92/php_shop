<?php

use App\Controller\Product;

require_once $_SERVER['DOCUMENT_ROOT'] . '/../App/bootstrap.php';

$product_id = (int) $_GET['product_id'] ?? 0;

Product::edit($product_id);