<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\ServiceProvider;

use Illuminate\Support\ServiceProvider;

class JsonRpcRouteServiceProvider extends ServiceProvider
{
    protected $namespace;

    public function boot()
    {
        if ($this->routesAreCached()) {
            $this->loadCachedRoutes();
        } else {
            $this->loadRoutes();
        }
    }

    protected function loadCachedRoutes()
    {
        $this->app->booted(function () {
            require $this->getCachedRoutesPath();
        });
    }

    protected function loadRoutes()
    {
        if (method_exists($this, 'map')) {
            $this->app->call([$this, 'map']);
        }
    }

    protected function getCachedRoutesPath(): string
    {
        return $this->app->bootstrapPath(env('JSON_RPC__ROUTES_CACHE', 'cache/json_rpc_routes.php'));
    }

    protected function routesAreCached(): bool
    {
        return $this->app['files']->exists($this->getCachedRoutesPath());
    }
}
