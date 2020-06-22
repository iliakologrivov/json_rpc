<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Exception;

use IliaKologrivov\LaravelJsonRpcServer\Server\ErrorCode;

final class InvalidParamsException extends JsonRpcException
{

    protected function getDefaultMessage(): string
    {
        return 'Invalid params';
    }

    protected function getDefaultCode(): int
    {
        return ErrorCode::INVALID_PARAMS;
    }

}