<?php

namespace IliaKologrivov\LaravelJsonRpcServer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class JsonRpcRoute
 * @method static \IliaKologrivov\LaravelJsonRpcServer\Contract\RouteRegistryInterface namespace(string $value)
 * @method static \IliaKologrivov\LaravelJsonRpcServer\Contract\RouteRegistryInterface middleware(string $value)
 * @method static \IliaKologrivov\LaravelJsonRpcServer\Contract\RouteRegistryInterface endPoint(string $value, $callback)
 * @method static \IliaKologrivov\LaravelJsonRpcServer\Contract\RouterInterface group(array $attributes, $callback)
 * @method static \IliaKologrivov\LaravelJsonRpcServer\Contract\RouterInterface method(string $method, $action)
 * @package IliaKologrivov\LaravelJsonRpcServer\Facades\
 */
class JsonRpcRoute extends Facade
{
    protected static function getFacadeAccessor()
    {
        return app('json-rpc-server')->router();
    }
}