<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\ServiceProvider;

use IliaKologrivov\LaravelJsonRpcServer\Contract\MiddlewareDispatcherInterface;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RouteCacheInterface;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RouterInterface;
use IliaKologrivov\LaravelJsonRpcServer\Server\MiddlewareDispatcher;
use IliaKologrivov\LaravelJsonRpcServer\Server\RouteFileCache;
use IliaKologrivov\LaravelJsonRpcServer\Server\RouteRegistry;
use Illuminate\Support\ServiceProvider;
use IliaKologrivov\LaravelJsonRpcServer\Console\RouteListCommand;
use IliaKologrivov\LaravelJsonRpcServer\Console\RouteCacheCommand;
use IliaKologrivov\LaravelJsonRpcServer\Console\RouteClearCommand;
use IliaKologrivov\LaravelJsonRpcServer\Contract\ServerInterface;
use IliaKologrivov\LaravelJsonRpcServer\Server\RequestFactory;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RequestFactoryInterface;
use IliaKologrivov\LaravelJsonRpcServer\Server\RouteDispatcher;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RouteDispatcherInterface;
use IliaKologrivov\LaravelJsonRpcServer\Server\Router;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RouteRegistryInterface;
use IliaKologrivov\LaravelJsonRpcServer\Server\Server;

class JsonRpcServerServiceProvider extends ServiceProvider
{
    public $bindings = [
        RequestFactoryInterface::class => RequestFactory::class,
        RouteRegistryInterface::class => RouteRegistry::class,
        RouteDispatcherInterface::class => RouteDispatcher::class,
        RouterInterface::class => Router::class,
        RouteCacheInterface::class => RouteFileCache::class,
        MiddlewareDispatcherInterface::class => MiddlewareDispatcher::class,
    ];

    public $singletons = [
        ServerInterface::class => Server::class,
    ];

    protected $namespace;

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../Providers/JsonRpcRouteServiceProvider.php' => app_path('/Providers/JsonRpcRouteServiceProvider2.php'),
        ], 'json-rpc-server');

        $this->commands([
            RouteCacheCommand::class,
            RouteClearCommand::class,
            RouteListCommand::class,
        ]);
    }

    public function register()
    {
        $this->app->alias(ServerInterface::class, 'json-rpc-server');
    }
}
