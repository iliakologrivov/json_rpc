<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Console;

use Illuminate\Console\Command;
use IliaKologrivov\LaravelJsonRpcServer\Contract\ServerInterface as JsonRpcServerContract;

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
     * @param  JsonRpcServerContract  $server
     * @return mixed
     */
    public function handle(JsonRpcServerContract $server)
    {
        $headers = [
            'Endpoint',
            'Method',
            'Controller',
        ];
        $data = [];

        foreach($server->router()->getRoutes() as $endpoint => $routes) {
            $endpoint = '/' . trim($endpoint, '/');

            foreach($routes as $method => $controller) {
                $data[] = [
                    'endpoint' => $endpoint,
                    'method' => $method,
                    'controller' => $controller[0] . '@' . $controller[1],
                ];
            }
        }

        $this->table($headers, $data);
    }
}
