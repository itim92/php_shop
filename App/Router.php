<?php


namespace App;


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
            die('404');
        }

        $controller = $this->container->get($route[0]);
        $this->container->getInjector()->callMethod($controller, $route[1]);
    }
}