<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Contract;

interface MiddlewareDispatcherInterface
{

    /**
     * @param array $middleware
     * @param mixed $context
     * @param callable $next
     * @return mixed
     */
    public function dispatch(array $middleware, $context, callable $next);

}
