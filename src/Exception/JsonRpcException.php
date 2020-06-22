<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Exception;

class JsonRpcException extends \RuntimeException
{
    protected $data = [];

    public function __construct(string $message = "", int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message ?: $this->getDefaultMessage(), intval($code ?: $this->getDefaultCode()), $previous);
    }

    protected function getDefaultMessage(): string
    {
        return '';
    }

    protected function getDefaultCode(): int
    {
        return 0;
    }

    public function setExtras($data): array
    {
        $this->data = $data;
    }

    public function getExtras(): array
    {
        return $this->data;
    }
}