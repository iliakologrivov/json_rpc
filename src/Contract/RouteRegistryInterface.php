<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Contract;

interface RouteRegistryInterface
{
    public function group($callback): RouterInterface;

    public function namespace(string $namespace): RouteRegistryInterface;

    public function middleware($middleware): RouteRegistryInterface;

    public function attribute(string $key, $value): RouteRegistryInterface;

}