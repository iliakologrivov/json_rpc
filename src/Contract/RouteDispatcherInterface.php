<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Contract;

interface RouteDispatcherInterface
{

    /**
     * @param RouteInterface $route
     * @param RequestInterface $request
     * @return mixed
     */
    public function dispatch(RouteInterface $route, RequestInterface $request);

}
