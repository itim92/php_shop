<?php

use App\Db\MySQL;

define('APP_DIR', __DIR__ . '/../');

require_once APP_DIR . '/vendor/autoload.php';
$config = require_once APP_DIR . '/config/config.php';

$smarty = new Smarty();

$smarty->template_dir = $config['template']['template_dir'];
$smarty->compile_dir = $config['template']['compile_dir'];
$smarty->cache_dir = $config['template']['cache_dir'];

/**
 * @return MySQL
 */
function db() {
    global $config;
    static $mysql;

    if (is_null($mysql)) {
        $mysql = new MySQL($config['db']['host'], $config['db']['user'], $config['db']['password'], $config['db']['db_name']);
    }

    return $mysql;
}

function smarty() {
    global $config;
    static $smarty;

    if (is_null($smarty)) {
        $smarty = new Smarty();

        $smarty->template_dir = $config['template']['template_dir'];
        $smarty->compile_dir = $config['template']['compile_dir'];
        $smarty->cache_dir = $config['template']['cache_dir'];
    }

    return $smarty;
}

function user() {
    static $user;

    /**
     * @var $user \App\Model\User
     */

    if (is_null($user)) {
        $user = new \App\Model\User();

        if (isset($_SESSION['user_id'])) {
            $user_id = (int) $_SESSION['user_id'];
            $user = \App\Service\UserService::getById($user_id);
        }
    }

    return $user;
}


session_start();


smarty()->assign_by_ref('user', user());