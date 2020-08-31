<?php

namespace App\Providers;

use IliaKologrivov\LaravelJsonRpcServer\Facades\JsonRpcRoute;
use IliaKologrivov\LaravelJsonRpcServer\ServiceProvider\JsonRpcRouteServiceProvider as ServiceProvider;

class JsonRpcRouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\JsonRpc\Controllers';

    public function map()
    {
        JsonRpcRoute::prefix('json_rpc')
            ->namespace($this->namespace)
            ->middleware([])
            ->group(base_path('routes/json_rpc.php'));
    }
}
