<?php


use App\Middleware\CartMiddleware;

return [
    CartMiddleware::class,
    \App\Middleware\UserMiddleware::class,
];