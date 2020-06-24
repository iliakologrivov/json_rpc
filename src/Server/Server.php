<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Server;

use IliaKologrivov\LaravelJsonRpcServer\Contract\RouterInterface;
use Illuminate\Http\JsonResponse;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RequestInterface;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RouteDispatcherInterface;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RequestExecutorInterface;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RequestFactoryInterface;
use IliaKologrivov\LaravelJsonRpcServer\Contract\ServerInterface;
use IliaKologrivov\LaravelJsonRpcServer\Exception\Handler;

class Server implements ServerInterface, RequestExecutorInterface
{
    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RouteDispatcherInterface
     */
    private $routeDispatcher;

    /**
     * @var Handler 
     */
    private $exceptionHandler;

    public function __construct(
        RequestFactoryInterface $requestFactory,
        RouterInterface $router,
        RouteDispatcherInterface $routeDispatcher,
        Handler $exceptionHandler
    ) {
        $this->requestFactory = $requestFactory;
        $this->router = $router;
        $this->routeDispatcher = $routeDispatcher;
        $this->exceptionHandler = $exceptionHandler;
    }

    public function router(): RouterInterface
    {
        return $this->router;
    }

    public function run(string $endpoint, string $payload = null): JsonResponse
    {
        try {
            /**
             * @var Batch $batch
             * @var array|null $response
             */
            $batch = $this->requestFactory->createFromPayload($endpoint, $payload);
            $response = $batch->executeWith($this);
        } catch (\Throwable $exception) {
            $response = $this->handleException($exception, null);
        }

        return new JsonResponse($response);
    }

    public function execute(RequestInterface $request): ?Response
    {
        try {
            $route = $this->router->resolve($request->getEndpoint(), $request->getMethod());

            $result = $this->routeDispatcher->dispatch($route, $request);

            //Если нет id то это означает, что ответ не интересует и от сервера ответ не нужен.
            return $request->hasId() ? new Response($request->getId(), $result) : null;
        } catch (\Throwable $exception) {
            return $this->handleException($exception, $request);
        }
    }

    private function handleException(\Throwable $exception, RequestInterface $request = null): Response
    {
        $this->exceptionHandler->report($exception);

        return $this->exceptionHandler->render($exception, $request);
    }
}
