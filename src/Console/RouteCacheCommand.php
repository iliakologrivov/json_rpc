<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Console;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\RouteCollection;

class RouteCacheCommand extends Command
{
    protected $name = 'json-rpc-route:cache';

    protected $description = 'Create a route cache file for faster route registration';

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    public function handle()
    {
        $this->call('json-rpc-route:clear');

        $routes = $this->getFreshApplicationRoutes();

        if ($routes === []) {
            return $this->error('Your application doesn\'t have any routes.');
        }

        $filePath = $this->getLaravel()->bootstrapPath(env('JSON_RPC__ROUTES_CACHE', 'cache/json_rpc_routes.php'));

        $this->files->put(
            $filePath, $this->buildRouteCacheFile($routes)
        );

        $this->info('Routes cached successfully!');
    }

    protected function getFreshApplicationRoutes(): array
    {
        return $this->getFreshApplication()['json-rpc-server']->router()->getRoutes();
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

    protected function buildRouteCacheFile(array $routes): string
    {
        $stub = $this->files->get(__DIR__.'/stubs/routes.stub');

        return str_replace('{{routes}}', base64_encode(serialize($routes)), $stub);
    }
}
