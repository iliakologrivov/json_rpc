<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Contract;

use IliaKologrivov\LaravelJsonRpcServer\Server\Response;

interface RequestExecutorInterface
{
    public function execute(RequestInterface $request): ?Response;

}