<?php


function check_mysqli_query_errors($mysqli_connect) {
    $mysqli_errno = mysqli_errno($mysqli_connect);
    if ($mysqli_errno) {
        $mysqli_error = mysqli_error($mysqli_connect);
        $message = "Mysql query error: ($mysqli_errno) $mysqli_error";
        die($message);
    }
}