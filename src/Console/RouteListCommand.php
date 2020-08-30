<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Console;

use Illuminate\Console\Command;
use IliaKologrivov\LaravelJsonRpcServer\Contract\ServerInterface;

class RouteListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'json-rpc-route:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Route list json rpc';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param  ServerInterface  $server
     * @return mixed
     */
    public function handle(ServerInterface $server)
    {
        $headers = [
            'Endpoint',
            'Method',
            'Controller',
            'Middleware',
        ];
        $data = [];

        foreach($server->router()->getRoutes() as $endpoint => $routes) {
            foreach($routes as $method => $route) {
                $row = [
                    'endpoint' => $endpoint,
                    'method' => $method,
                    'controller' => null,
                    'middleware' => '[' . implode(', ', $route[2]) . ']',
                ];

                if ($route[1] instanceof \Closure) {
                    $row['controller'] = 'Closure';
                } else {
                    $row['controller'] = $route[0] . '@' . $route[1];
                }

                $data[] = $row;
            }
        }

        $this->table($headers, $data);
    }
}
