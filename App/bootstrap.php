<?php

use App\Config;
use App\Db\MySQL;
use App\Di\Container;
use App\Http\Request;
use App\Http\Response;
use App\Kernel;
use App\Service\CartService;
use App\Service\UserService;

define('APP_DIR', __DIR__ . '/../');

require_once APP_DIR . '/vendor/autoload.php';

session_start();

$container = new Container();

$container->singletone(Response::class);
$container->singletone(Request::class);
$container->singletone(CartService::class);
$container->singletone(UserService::class);

$container->singletone(MySQL::class, function() use ($container) {
    $config = $container->get(Config::class);
    $host = $config->get('db.host');
    $user_name = $config->get('db.user');
    $user_pwd = $config->get('db.password');
    $db_name = $config->get('db.db_name');

    return new MySQL($host, $user_name, $user_pwd, $db_name);
});

$container->singletone(Config::class, function() {
    $config_path = APP_DIR . '/config/config.php';
    $default_configs_path = APP_DIR . '/config.d';

    return new Config($config_path, $default_configs_path);
});

$container->singletone(Smarty::class, function() use ($container) {
    $config = $container->get(Config::class);
    $smarty = new Smarty();

    $smarty->template_dir = $config->get('template.template_dir');
    $smarty->compile_dir = $config->get('template.compile_dir');
    $smarty->cache_dir = $config->get('template.cache_dir');

    return $smarty;
});

$kernel = $container->get(Kernel::class);

