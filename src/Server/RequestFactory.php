<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Server;

use IliaKologrivov\LaravelJsonRpcServer\Contract\RequestFactoryInterface as RequestFactoryContract;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RequestInterface;
use IliaKologrivov\LaravelJsonRpcServer\Exception\BadRequestException;
use IliaKologrivov\LaravelJsonRpcServer\Contract\ExecutableInterface;

class RequestFactory implements RequestFactoryContract
{
    public function createFromPayload(string $endpoint, string $payloadJson): ExecutableInterface
    {
        try {
            $payload = json_decode($payloadJson);
        } catch (\Exception $e) {
            throw new BadRequestException();
        }

        if (is_array($payload)) {
            if ($payload === []) {
                throw new BadRequestException();
            }

            return new Batch($endpoint, $payload, $this);
        } elseif (is_object($payload)) {
            return $this->createSingleRequest($endpoint, $payload);
        }

        throw new BadRequestException();
    }

    public function createRequest(string $endpoint, \stdClass $requestData): RequestInterface
    {
        return $this->createSingleRequest($endpoint, $requestData);
    }

    private function createSingleRequest(string $endpoint, \stdClass $requestData): Request
    {
        if (($requestData->jsonrpc ?? null) !== '2.0') {
            throw new BadRequestException();
        }

        if (empty($requestData->method) || !is_string($requestData->method)) {
            throw new BadRequestException();
        }

        $params = null;

        if (! empty($requestData->params)) {
            if (is_array($requestData->params)) {
                $params = RequestParams::constructPositional($requestData->params);
            } elseif (is_object($requestData->params)) {
                $params = RequestParams::constructNamed((array)$requestData->params);
            } else {
                throw new BadRequestException();
            }
        }

        return new Request($endpoint, $requestData->method, $params, isset($requestData->id) ? $requestData->id : null);
    }
}
