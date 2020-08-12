<?php

namespace App\Service;

use App\Controller\ControllerInterface;

final class RouterService
{
    /** @var array<string, string> */
    private array $routes;

    private string $default;

    public function __construct()
    {
        $routeConfig = include dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'src/config/route.php';

        $this->routes = $routeConfig['routes'];
        $this->default = $routeConfig['default'];
    }

    public function callController(string $route): void
    {
        $controllerName = $this->routes[$route] ?? $this->default;

        $controller = new $controllerName;
        assert($controller instanceof ControllerInterface);

        $controller->run();
    }
}