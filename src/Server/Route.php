<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Server;

use IliaKologrivov\LaravelJsonRpcServer\Contract\RouteInterface;

final class Route implements RouteInterface
{
    private $controller;

    private $action;

    private $middleware = [];

    /**
     * @param  string|null  $controller
     * @param  string|callable  $action
     * @param  string[]  $middleware
     */
    public function __construct(?string $controller, $action, array $middleware = [])
    {
        $this->controller = $controller;
        $this->action = $action;
        $this->middleware = $middleware;
    }

    public function getController(): ?string
    {
        return $this->controller;
    }

    /**
     * @return string|callable
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function isControllerAction(): bool
    {
        return $this->controller !== null && is_string($this->action);
    }
}