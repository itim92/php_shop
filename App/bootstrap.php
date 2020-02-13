<?php

use App\Db\MySQL;
use App\Model\Cart;
use App\Model\User;
use App\Service\CartService;
use App\Service\UserService;

define('APP_DIR', __DIR__ . '/../');

require_once APP_DIR . '/vendor/autoload.php';
$config = require_once APP_DIR . '/config/config.php';

$smarty = new Smarty();

$smarty->template_dir = $config['template']['template_dir'];
$smarty->compile_dir = $config['template']['compile_dir'];
$smarty->cache_dir = $config['template']['cache_dir'];

session_start();


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
     * @var $user User
     */

    if (is_null($user)) {
        $user = new User();

        if (isset($_SESSION['user_id'])) {
            $user_id = (int) $_SESSION['user_id'];
            $user = UserService::getById($user_id);
        }
    }

    return $user;
}

/**
 * @return Cart
 */
function cart() {
    static $cart;

    if (is_null($cart)) {
        $cart = CartService::getCart();
    }

    return $cart;
}

$user = user();
$cart = cart();

smarty()->assign_by_ref('user', $user);
smarty()->assign_by_ref('cart', $cart);
