<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/../App/bootstrap.php';

$vendor_id = (int) $_GET['vendor_id'] ?? 0;

if ($vendor_id) {
    $query = "SELECT * FROM vendors WHERE id = $vendor_id";
    $result = mysqli_query($connect, $query);

    check_mysqli_query_errors($connect);

    $vendor = mysqli_fetch_assoc($result);
    $smarty->assign_by_ref('vendor', $vendor);
}



$smarty->display('vendor/edit.tpl');