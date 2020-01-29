<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/../App/bootstrap.php';

$query = "SELECT * FROM vendors";
$result = mysqli_query($connect, $query);

check_mysqli_query_errors($connect);

$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

$smarty->assign_by_ref('vendors', $data);
$smarty->display('vendor/index.tpl');