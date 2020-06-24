<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Contract;

use IliaKologrivov\LaravelJsonRpcServer\Contract\RouteInterface as RouteContract;

interface RouterInterface
{

    public function method(string $method, $action): RouterInterface;

    public function group(array $attributes, $callback): RouterInterface;

    public function resolve(string $endpoint, string $method): RouteContract;

    public function getRoutes(): array;

    public function setRoutes(array $routes): RouterInterface;

}