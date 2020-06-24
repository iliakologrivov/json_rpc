<?php

namespace App\Providers;

use \IliaKologrivov\LaravelJsonRpcServer\Facades\JsonRpcRoute;
use IliaKologrivov\LaravelJsonRpcServer\ServiceProvider\JsonRpcRouteServiceProvider as ServiceProvider;

class JsonRpcRouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Http\Controllers';

    public function boot()
    {
        //
        parent::boot();
    }

    public function map()
    {
        JsonRpcRoute::namespace($this->namespace)
            ->group(base_path('routes/json_rpc.php'));
    }
}
