<?php
declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Contract;

interface RouteInterface
{
    /**
     * @return string
     */
    public function getController(): ?string;

    /**
     * @return string|callable
     */
    public function getAction();

    /**
     * @return array
     */
    public function getMiddleware(): array;

    /**
     * @return bool
     */
    public function isControllerAction(): bool;
}