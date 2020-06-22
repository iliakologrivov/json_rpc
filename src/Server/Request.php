<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Server;

use Illuminate\Contracts\Support\Jsonable;
use IliaKologrivov\LaravelJsonRpcServer\Contract\ExecutableInterface;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RequestInterface as RequestContract;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RequestExecutorInterface;

class Request implements RequestContract, ExecutableInterface
{

    /**
     * @var string
     */
    private $method;

    /**
     * @var RequestParams|null
     */
    private $params;

    /**
     * @var null|string|int
     */
    private $id = null;

    /**
     * @param string $method
     * @param RequestParams|null $params
     * @param string|int|null $id
     */
    public function __construct(string $method, RequestParams $params = null, $id = null)
    {
        $this->method = (string)$method;
        $this->params = $params ?: RequestParams::constructEmpty();

        if (null !== $id) {
            if (!is_int($id)) {
                $id = (string)$id;
            }
        }

        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return RequestParams
     */
    public function getParams(): RequestParams
    {
        return $this->params;
    }

    /**
     * @return bool
     */
    public function hasId(): bool
    {
        return $this->id !== null;
    }

    /**
     * @return null|string|int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param RequestExecutorInterface $executor
     * @return Jsonable
     */
    public function executeWith(RequestExecutorInterface $executor)
    {
        return $executor->execute($this);
    }

    public function all(): ?array
    {
        return $this->getParams()->getParams();
    }

    /**
     * @param int|string $key
     * @return bool
     */
    public function has($key): bool
    {
        return $this->getParams()->has($key);
    }

    public function get($key, $default = null)
    {
        return $this->getParams()->get($key, $default);
    }
}
