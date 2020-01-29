<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/../App/bootstrap.php';

$folder_id = (int) $_GET['folder_id'] ?? 0;

if ($folder_id) {
    $query = "SELECT * FROM folders WHERE id = $folder_id";
    $result = mysqli_query($connect, $query);

    check_mysqli_query_errors($connect);

    $folder = mysqli_fetch_assoc($result);
    $smarty->assign_by_ref('folder', $folder);
}


$smarty->display('folder/edit.tpl');