<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Server;

use IliaKologrivov\LaravelJsonRpcServer\Contract\RouteCacheInterface;
use \Illuminate\Contracts\Foundation\Application;

final class RouteFileCache implements RouteCacheInterface
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function has(): bool
    {
        return $this->app['files']->exists($this->getCachedRoutesPath());
    }

    public function clear(): void
    {
        $this->app['files']->delete($this->getCachedRoutesPath());
    }

    public function make(array $routes): bool
    {
        $this->app['files']->put(
            $this->getCachedRoutesPath(), json_encode($routes)
        );

        return true;
    }

    public function get(): array
    {
        return json_decode($this->app['files']->get($this->getCachedRoutesPath()), true);
    }

    private function getCachedRoutesPath(): string
    {
        return $this->app->bootstrapPath(env('JSON_RPC__ROUTES_CACHE', 'cache/json_rpc_routes.json'));
    }
}