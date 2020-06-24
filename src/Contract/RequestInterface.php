<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Contract;

use IliaKologrivov\LaravelJsonRpcServer\Server\RequestParams;

interface RequestInterface
{
    public function getEndpoint(): string;

    public function getMethod(): string;

    public function getParams(): RequestParams;

    /**
     * @return null|string|int
     */
    public function getId();

    public function hasId(): bool;

    public function all(): ?array;

    public function has($key): bool;

    public function get($key, $default = null);
}
