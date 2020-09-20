<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Console;

use IliaKologrivov\LaravelJsonRpcServer\Contract\RouteCacheInterface;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;

class RouteCacheCommand extends Command
{
    protected $name = 'json-rpc-route:cache';

    protected $description = 'Create a route cache file for faster route registration';

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(RouteCacheInterface $cache)
    {
        try {
            $this->call('json-rpc-route:clear');

            $routes = $this->getFreshApplicationRoutes();

            foreach ($routes as $endpoint => $methods) {
                foreach ($methods as $method => $route) {
                    if ($route[1] instanceof \Closure) {
                        throw new \Exception('Unable to prepare route ' . $endpoint . ' [' . $method . '] for serialization. Uses Closure.');
                    }
                }
            }

            if ($routes === []) {
                throw new \Exception('Your application doesn\'t have any routes.');
            }

            $cache->make($routes);

            $this->info('Routes cached successfully!');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    protected function getFreshApplicationRoutes(): array
    {
        return $this->getFreshApplication()['json-rpc-server']
            ->router()
            ->getRoutes();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application
     */
    protected function getFreshApplication()
    {
        return tap(require $this->laravel->bootstrapPath().'/app.php', function ($app) {
            $app->make(ConsoleKernelContract::class)->bootstrap();
        });
    }
}
