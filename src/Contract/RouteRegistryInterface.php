<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Contract;

interface RouteRegistryInterface
{
    public function group($callback): RouterInterface;

    public function namespace(string $namespace): RouteRegistryInterface;

    public function middleware(array $middleware): RouteRegistryInterface;

    public function prefix(string $endpoint): RouteRegistryInterface;
}