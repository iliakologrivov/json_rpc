<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Server;

use IliaKologrivov\LaravelJsonRpcServer\Contract\RouterInterface;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RouteRegistryInterface;

class RouteRegistry implements RouteRegistryInterface
{
    private $router;

    private $attributes = [];

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function group($callback): RouterInterface
    {
        return $this->router->group($this->attributes, $callback);
    }

    public function namespace(string $namespace): RouteRegistryInterface
    {
        $this->attributes['namespace'] = $namespace;

        return $this;
    }

    public function middleware($middleware): RouteRegistryInterface
    {
        $this->attributes['middleware'] = $middleware;

        return $this;
    }

    public function attribute(string $key, $value): RouteRegistryInterface
    {
        $this->attributes[$key] = $value;

        return $this;
    }
}