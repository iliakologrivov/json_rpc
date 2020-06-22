<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Server;

use IliaKologrivov\LaravelJsonRpcServer\Contract\RouteInterface;

final class Route implements RouteInterface
{
    private $controllerClass;

    private $actionName;

    /**
     * @param string $controllerClass
     * @param string $actionName
     */
    public function __construct(string $controllerClass, string $actionName) {
        $this->controllerClass = $controllerClass;
        $this->actionName = $actionName;
    }

    /**
     * @return string
     */
    public function getControllerClass(): string
    {
        return $this->controllerClass;
    }

    /**
     * @return string
     */
    public function getActionName(): string
    {
        return $this->actionName;
    }
}