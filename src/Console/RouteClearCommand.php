<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Console;

use IliaKologrivov\LaravelJsonRpcServer\Contract\RouteCacheInterface;
use Illuminate\Console\Command;

class RouteClearCommand extends Command
{
    protected $name = 'json-rpc-route:clear';

    protected $description = 'Remove the route cache file';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  RouteCacheInterface  $cache
     * @return void
     */
    public function handle(RouteCacheInterface $cache)
    {
        $cache->clear();

        $this->info('Route cache cleared!');
    }
}
