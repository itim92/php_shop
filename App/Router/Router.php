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
//        $routes = $this->config->get('routes');

        $routes = $this->getRoutes();
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

    private function getRoutes() {
        $controllers = $this->config->get('controllers');


        $routes = [];

        foreach ($controllers as $controller) {
            $reflection_controller = new \ReflectionClass($controller);
            $methods = $reflection_controller->getMethods();

            foreach ($methods as $method) {
                /**
                 * @var $method \ReflectionMethod
                 */

                $doc_comment = $method->getDocComment();

                $matches = [];
                preg_match_all('/@Route\(.+\)/im', $doc_comment, $matches);

                if (empty($matches[0])) {
                    continue;
                }

                $annotation_routes = $matches[0];

                foreach ($annotation_routes as $annotate_route) {

                    $annotate_params = str_replace('@Route(', '', $annotate_route);
                    $annotate_params = str_replace(')', '', $annotate_params);

                    $annotate_params = explode(',', $annotate_params);
                    $annotate_params = array_map(function($item) {
                        return trim($item);
                    }, $annotate_params);

                    $params = [];

                    foreach ($annotate_params as $param_str) {
                        $param_data = explode('=', $param_str);
                        $key = $param_data[0];
                        $value = $param_data[1];

                        $value = str_replace("\"", "", $value);

                        $params[$key] = $value;
                    }


                    $routes[$params['url']] = [
                        $controller, $method->getName(),
                    ];

                }
            }
        }

        return $routes;

    }

    private function notFound() {
        die('404');
    }
}