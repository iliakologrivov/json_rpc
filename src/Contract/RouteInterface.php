<?php
declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Contract;

interface RouteInterface
{
    /**
     * @return string
     */
    public function getControllerClass(): string;

    /**
     * @return string
     */
    public function getActionName(): string;
}