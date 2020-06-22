<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Server;

use \Illuminate\Http\Request;
use IliaKologrivov\LaravelJsonRpcServer\Contract\ServerInterface as JsonRpcServerContract;

class Controller
{
    public function run(Request $request, JsonRpcServerContract $server)
    {
        return $server->run($request->path(), $request->getContent());
    }
}