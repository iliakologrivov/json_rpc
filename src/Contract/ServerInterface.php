<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Contract;

use Illuminate\Http\JsonResponse;

interface ServerInterface
{

    /**
     * @return RouteRegistryInterface
     */
    public function router(): RouteRegistryInterface;

    /**
     * @param string $endpoint
     * @param string $payload
     * @return JsonResponse
     */
    public function run(string $endpoint, string $payload): JsonResponse;

}