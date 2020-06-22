<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Contract;

interface ExceptionHandlerInterface
{
    public function render(RequestInterface $request, \Throwable $exception);
}
