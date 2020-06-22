<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Exception;

use IliaKologrivov\LaravelJsonRpcServer\Server\ErrorCode;

final class ParseErrorException extends JsonRpcException
{

    protected function getDefaultMessage(): string
    {
        return 'Parse error';
    }

    protected function getDefaultCode(): int
    {
        return ErrorCode::PARSE_ERROR;
    }

}