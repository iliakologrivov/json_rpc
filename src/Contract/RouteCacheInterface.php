<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Contract;

interface RouteCacheInterface
{
    public function has(): bool;

    public function clear(): void;

    public function make(array $routes): bool;

    public function get(): array;
}