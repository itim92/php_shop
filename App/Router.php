<?php


namespace App;


use App\Service\RequestService;

class Router
{
    /**
     * @var Factory
     */
    private $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function route() {
        $request_uri = $_SERVER['REQUEST_URI'];

        $request_data = explode('?', $request_uri);
        $request_url = $request_data[0];

        $routers = require_once APP_DIR . '/config/routing.php';

        $route = $routers[$request_url] ?? null;

        if (is_null($route)) {
            die('404');
        }

        $class = $route[0];
        $method = $route[1];

        $reflectionClassController = new \ReflectionClass($class);
        if (!$reflectionClassController->hasMethod($method)) {
            die('503 method does not exist');
        }

        $reflectionMethod = $reflectionClassController->getMethod($method);

        $arguments = [];

        foreach ($reflectionMethod->getParameters() as $parameter) {
            $reflectionParameterClass = $parameter->getClass();
//            echo '<pre>'; var_dump($reflectionParameterClass); echo '</pre>';
            $className = $reflectionParameterClass->getName();

//            $arguments[] = new $className();
            $arguments[] = $this->factory->getInstance($className);
        }


        call_user_func_array($route, $arguments);
    }


    //    private static $main = '/product/list.php';

//    public static function route() {
////        echo '<pre>'; var_dump($_SERVER); echo '</pre>';
//
//        $path = $_SERVER['DOCUMENT_ROOT'] . '/../Router/';
//        $request_uri = $_SERVER['REQUEST_URI'];
//
//        $request_data = explode('?', $request_uri);
//
//        $request_url = $request_data[0];
//
//        if ($request_url == '/') {
//            $request_url = static::$main;
//        }
//
//        if (preg_match('/\/$/i', $request_url)) {
//            $request_url .= 'index';
//        }
//
//        $script_path = $path . $request_url . '.php';
//
//        echo '<pre>'; var_dump($script_path); echo '</pre>';
//
//        if (file_exists($script_path) && !is_dir($script_path)) {
//            require_once $script_path;
//        } else {
//            die('404');
//        }
//
//
//    }
}