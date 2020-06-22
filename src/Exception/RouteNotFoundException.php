<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Exception;

use IliaKologrivov\LaravelJsonRpcServer\Server\ErrorCode;

final class RouteNotFoundException extends JsonRpcException
{
    public function __construct(string $method = null, int $code = 0, \Exception $previous = null)
    {
        parent::__construct($method ? "Method '{$method}' not found" : $this->getDefaultMessage(), $code, $previous);
    }

    protected function getDefaultMessage(): string
    {
        return 'Method not found';
    }

    protected function getDefaultCode(): int
    {
        return ErrorCode::METHOD_NOT_FOUND;
    }
}