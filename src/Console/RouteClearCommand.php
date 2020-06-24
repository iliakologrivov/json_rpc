<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class RouteClearCommand extends Command
{
    protected $name = 'json-rpc-route:clear';

    protected $description = 'Remove the route cache file';

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $filePath = $this->getLaravel()->bootstrapPath(env('JSON_RPC__ROUTES_CACHE', 'cache/json_rpc_routes.php'));

        $this->files->delete($filePath);

        $this->info('Route cache cleared!');
    }
}
