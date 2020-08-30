<?php
declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Server;

use Illuminate\Contracts\Container\Container;
use Illuminate\Pipeline\Pipeline;
use IliaKologrivov\LaravelJsonRpcServer\Contract\MiddlewareDispatcherInterface;

final class MiddlewareDispatcher implements MiddlewareDispatcherInterface
{

    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param array $middleware
     * @param mixed $context
     * @param callable $next
     * @return mixed
     */
    public function dispatch(array $middleware, $context, callable $next)
    {
        return (new Pipeline($this->container))->send($context)
            ->through($middleware)
            ->then($next);
    }

}