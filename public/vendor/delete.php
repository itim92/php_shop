<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/../App/bootstrap.php';

$vendor_id = (int) $_POST['vendor_id'] ?? 0;

if (!$vendor_id) {
    die('id required');
}
$query = "DELETE FROM vendors WHERE id = $vendor_id";

mysqli_query($connect, $query);

check_mysqli_query_errors($connect);

header('Location: /vendor/');
