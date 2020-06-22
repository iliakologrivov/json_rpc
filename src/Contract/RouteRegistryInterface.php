<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Contract;

interface RouteRegistryInterface
{
    /**
     * @param array $attributes
     * @param callable|string $callback
     * @return RouteRegistryInterface
     */
    public function group(array $attributes, $callback): RouteRegistryInterface;

    /**
     * @param string $method
     * @return RouteInterface
     */
    public function resolve(string $method): RouteInterface;

}