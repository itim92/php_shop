<?php


namespace App\Controller;


use App\Http\Request;
use App\Http\Response;
use App\Http\ResponseBody\JSONBody;
use App\Http\ResponseBody\TextBody;
use App\Router\Route;

abstract class AbstractController
{

    /**
     * @var \Smarty
     */
    private $smarty;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Route
     */
    protected $route;

    /**
     * @var array
     */
    protected $shared_data = [];

    public function __construct(\Smarty $smarty, Request $request, Response $response)
    {
        $this->smarty = $smarty;
        $this->request = $request;
        $this->response = $response;
    }

    public function setRoute(Route $route) {
        $this->route = $route;
    }

    public function getRoute() {
        return $this->route;
    }

    protected function render(string $template_name, array $params) {

        foreach ($this->shared_data as $key => &$value) {
            if (is_scalar($value)) {
                $this->smarty->assign($key, $value);
            } else {
                $this->smarty->assign_by_ref($key, $value);
            }
        }

        foreach ($params as $key => &$value) {
            if (is_scalar($value)) {
                $this->smarty->assign($key, $value);
            } else {
                $this->smarty->assign_by_ref($key, $value);
            }
        }

        $body = new TextBody($this->smarty->fetch($template_name));
//        $body = new JSONBody($params);
        $this->response->setBody($body);
//        $this->response->setHeader('X-SOME-HEADER', 'HELLO WORLD');

        return $this->response;
    }

    protected function json(array $params) {
        $body = new JSONBody($params);
        $this->response->setBody($body);

        $this->response->setHeader('Content-type', 'application/json');

        return $this->response;
    }

    protected function redirect(string $url) {
        $this->response->redirect($url);

        return $this->response;
    }

    public function addSharedData(string $key, $value) {
        $this->shared_data[$key] = $value;
    }

}