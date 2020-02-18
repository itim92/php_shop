<?php


namespace App\Router;


use App\Config;
use App\Controller\Exception\MethodDoesNotExistException;
use App\Di\Container;
use App\Http\Request;
use App\Service\RequestService;

class Router
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Config
     */
    private $config;



    public function __construct(Container $container, Request $request, Config $config)
    {
        $this->request = $request;
        $this->config = $config;
        $this->container = $container;
    }

    public function dispatch() {
        $routes = $this->config->get('routes');
        $url = $this->request->getUrl();
        $route = $routes[$url] ?? null;

        if (is_null($route)) {
            $this->notFound();
        }

        $controller = $this->container->get($route[0]);
        $method = $route[1];

        try {
            $route = new Route($controller, $method);
        } catch (MethodDoesNotExistException $exception) {
            $this->notFound();
        }

        return $route;
    }

    private function notFound() {
        die('404');
    }
}