<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\ServiceProvider;

use Illuminate\Support\ServiceProvider;
use IliaKologrivov\LaravelJsonRpcServer\Console\RouteListCommand;
use IliaKologrivov\LaravelJsonRpcServer\Contract\ServerInterface as JsonRpcServerContract;
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
        RouteRegistryInterface::class => Router::class,
        RouteDispatcherInterface::class => RouteDispatcher::class,
    ];

    public $singletons = [
        JsonRpcServerContract::class => Server::class,
    ];

    public function boot()
    {
        $this->commands([
            RouteListCommand::class,
        ]);
    }

    public function register()
    {

    }

}
