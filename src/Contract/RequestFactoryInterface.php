<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Contract;

interface RequestFactoryInterface
{

    /**
     * @param string $endpoint
     * @param string $payloadJson
     * @return ExecutableInterface
     */
    public function createFromPayload(string $endpoint, string $payloadJson): ExecutableInterface;

    /**
     * @param string $endpoint
     * @param \stdClass $requestData
     * @return RequestInterface
     */
    public function createRequest(string $endpoint, \stdClass $requestData): RequestInterface;

}