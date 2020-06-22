<?php

namespace IliaKologrivov\LaravelJsonRpcServer\Facades;

use Illuminate\Support\Facades\Facade;
use IliaKologrivov\LaravelJsonRpcServer\Contract\ServerInterface as JsonRpcServerContract;

class JsonRpcRoute extends Facade
{
    protected static function getFacadeAccessor()
    {
        return app(JsonRpcServerContract::class)
            ->router();
    }
}