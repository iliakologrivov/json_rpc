<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Exception;

use IliaKologrivov\LaravelJsonRpcServer\Server\ErrorCode;

final class BadRequestException extends JsonRpcException
{

    protected function getDefaultMessage(): string
    {
        return 'Invalid Request';
    }

    protected function getDefaultCode(): int
    {
        return ErrorCode::INVALID_REQUEST;
    }

}