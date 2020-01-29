<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/../App/bootstrap.php';

$product_id = (int) $_GET['product_id'] ?? 0;

\App\Controller\Product::edit($product_id);