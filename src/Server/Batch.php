<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Server;

use Illuminate\Contracts\Support\Arrayable;
use IliaKologrivov\LaravelJsonRpcServer\Contract\ExecutableInterface;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RequestExecutorInterface;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RequestFactoryInterface;
use IliaKologrivov\LaravelJsonRpcServer\Exception\BadRequestException;

final class Batch implements ExecutableInterface, Arrayable
{
    private $endpoint;

    private $batch;

    private $requestFactory;

    public function __construct(string $endpoint, array $batch, RequestFactoryInterface $requestFactory)
    {
        $this->endpoint = $endpoint;
        $this->batch = $batch;
        $this->requestFactory = $requestFactory;
    }

    public function executeWith(RequestExecutorInterface $executor)
    {
        /**
         * @var Request[] $requests
         */
        $requests = array_map(function ($requestData) {
            if (!is_object($requestData)) {
                throw new BadRequestException();
            }

            return $this->requestFactory->createRequest($this->endpoint, $requestData);
        }, $this->batch);

        $responses = [];

        foreach ($requests as $request) {
            $response = $executor->execute($request);

            if (null !== $response) {
                $responses[] = $response->toArray();
            }
        }

        return $responses;
    }

    public function toArray()
    {
        return $this->batch;
    }

}
