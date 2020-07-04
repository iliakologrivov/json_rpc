<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\ServiceProvider;

use IliaKologrivov\LaravelJsonRpcServer\Contract\RouteCacheInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory;

class JsonRpcRouteServiceProvider extends ServiceProvider
{
    protected $namespace;

    /**
     * Bootstrap any application services.
     *
     * @param RouteCacheInterface $cache
     * @return void
     */
    public function boot(RouteCacheInterface $cache)
    {
        if ($cache->has()) {
            $this->app->booted(function () use ($cache) {
                $this->app['json-rpc-server']->router()->setRoutes($cache->get());
            });
        } else {
            $this->loadRoutes();
        }
    }

    protected function loadRoutes()
    {
        if (method_exists($this, 'map')) {
            $this->app->call([$this, 'map']);
        }
    }
}
