<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Contract;

use IliaKologrivov\LaravelJsonRpcServer\Server\RequestParams;

interface RequestInterface
{

    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @return RequestParams
     */
    public function getParams(): RequestParams;

    /**
     * @return null|string|int
     */
    public function getId();

    /**
     * @return bool
     */
    public function hasId(): bool;

    public function all(): ?array;

    public function has($key): bool;

    public function get($key, $default = null);
}
