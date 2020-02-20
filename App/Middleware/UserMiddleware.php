<?php


namespace App\Middleware;


use App\Di\Container;
use App\Repository\UserRepository;
use App\Router\Route;
use App\Service\UserService;

class UserMiddleware implements IMiddleware
{

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var Container
     */
    private $container;

    public function __construct(UserService $userService, Container $container)
    {
        $this->userService = $userService;
        $this->container = $container;
    }
    public function run(Route $route)
    {
        $controller = $route->getController();

        $userRepository = $this->container->get(UserRepository::class);

        $user = $this->userService->getCurrentUser($userRepository);
        $controller->addSharedData('user', $user);
    }
}