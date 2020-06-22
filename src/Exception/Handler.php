<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Exception;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Log\LoggerInterface;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RequestInterface;
use IliaKologrivov\LaravelJsonRpcServer\Server\Response;

class Handler
{
    /**
     * @var Container
     */
    protected $container;

    protected $internalDontReport = [
        ModelNotFoundException::class,
        ValidationFailedException::class,
    ];

    protected $dontReport = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Логирование исключений
     * @param  \Throwable  $exception
     * @return void
     * @throws \Exception
     */
    public function report(\Throwable $exception)
    {
        if ($this->shouldntReport($exception)) {
            return;
        }

        if (method_exists($exception, 'report')) {
            return $this->container->call([$exception, 'report']);
        }

        try {
            $logger = $this->container->make(LoggerInterface::class);
        } catch (\Exception $exception) {
            throw $exception;
        }

        $logger->error($exception->getMessage(), [
            'exception' => $exception
        ]);
    }

    /**
     * Проверка на, нужно ли логировать
     * @param  \Throwable  $exception
     * @return bool
     */
    protected function shouldntReport(\Throwable $exception): bool
    {
        $dontReport = array_merge($this->dontReport, $this->internalDontReport);

        foreach ($dontReport as $type) {
            if ($exception instanceof $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * Рендер исключения в ответ
     * @param  \Throwable  $exception
     * @param  RequestInterface|null  $request
     * @return Response
     */
    public function render(\Throwable $exception, ?RequestInterface $request = null): Response
    {
        if (method_exists($exception, 'render') && $response = $exception->render($request)) {
            if ($response instanceof Response) {
                return $response;
            }

            return new Response(($request !== null ? $request->getId() : null), $response, true);
        }

        if ($exception instanceof JsonRpcException) {
            return $this->convertJsonRpcException($exception, $request);
        }

        return new Response(
            $request !== null ? $request->getId() : null,
            $this->convertExceptionToArray($exception),
            true);
    }

    protected function convertJsonRpcException(JsonRpcException $exception, ?RequestInterface $request): Response
    {
        $defaultParams = [
            'code'    => $exception->getCode(),
            'message' => (string) $exception->getMessage()
        ];

        return new Response($request !== null ? $request->getId() : null,
            ($defaultParams + $exception->getExtras()),
            true);
    }

    protected function convertExceptionToArray(\Throwable $exception)
    {
        if (config('app.debug')) {
            return [
                'message' => $exception->getMessage(),
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => array_map(function($trace) {
                    unset($trace['args']);

                    return $trace;
                }, $exception->getTrace()),
            ];
        }

        return [
            'code' => $exception->getCode(),
            'message' => (string) $exception->getMessage(),
        ];
    }
}
