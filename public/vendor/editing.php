<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/../App/bootstrap.php';

$vendor_id = (int) $_POST['vendor_id'] ?? 0;
$name = (string) $_POST['name'] ?? '';

if (!$name) {
    die('Name required');
}

$name = mysqli_real_escape_string($connect, $name);

if ($vendor_id) {
    $query = "UPDATE vendors SET name = '$name' WHERE id = $vendor_id";
} else {
    $query = "INSERT INTO vendors(name) VALUES ('$name')";
}

mysqli_query($connect, $query);

check_mysqli_query_errors($connect);


header('Location: /vendor/');