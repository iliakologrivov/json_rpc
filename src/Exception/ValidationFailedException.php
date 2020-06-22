<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Exception;

use Illuminate\Contracts\Validation\Validator;
use IliaKologrivov\LaravelJsonRpcServer\Server\ErrorCode;

final class ValidationFailedException extends JsonRpcException
{

    private $validationErrors;

    public function __construct(Validator $validator, int $code = null)
    {
        parent::__construct(
            $this->getDefaultMessage(),
            $code ?: $this->getDefaultCode()
        );

        $this->validationErrors = $validator->errors()->toArray();
    }

    public function getExtras(): array
    {
        return [
            'data' => [
                'violations' => $this->validationErrors
            ]
        ];
    }

    protected function getDefaultMessage(): string
    {
        return 'Validation failed';
    }

    protected function getDefaultCode(): int
    {
        return ErrorCode::INVALID_PARAMS;
    }

}
