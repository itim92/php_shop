<?php


namespace App\Middleware;

use App\Router\Route;
use App\Service\UserService;

class UserMiddleware implements IMiddleware
{

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function run(Route $route)
    {
        $controller = $route->getController();

        $user = $this->userService->getCurrentUser();
        $controller->addSharedData('user', $user);
    }
}