<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Server;

final class RequestParams
{

    private $params;

    private $areParamsNamed;

    private function __construct(array $params, bool $areParamsNamed = false)
    {
        $this->params = $params;
        $this->areParamsNamed = $areParamsNamed;
    }

    public static function constructNamed(array $params)
    {
        return new self($params, true);
    }

    public static function constructPositional(array $params)
    {
        return new self(array_values($params), false);
    }

    public static function constructEmpty()
    {
        return new self([]);
    }

    /**
     * Returns the parameters.
     *
     * @return array An array of parameters
     */
    public function all(): array
    {
        return $this->params;
    }

    /**
     * Replaces the current parameters by a new set.
     *
     * @param  array  $params
     */
    public function replace(array $params = [])
    {
        $this->params = $params;
    }

    /**
     * Check for existance of a parameter.
     *
     * @param int|string $key
     * @return bool whether parameter $key exists
     */
    public function has($key): bool
    {
        return array_key_exists($key, $this->params);
    }

    /**
     * Get a parameter by name or index.
     *
     * @param int|string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->params[$key] : $default;
    }

    /**
     * @param int|string $key
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __get($key)
    {
        if (!$this->has($key)) {
            throw new \InvalidArgumentException("Parameter does not exist: '$key'");
        }

        return $this->params[$key];
    }

    /**
     * @return bool
     */
    public function areParamsNamed(): bool
    {
        return $this->areParamsNamed;
    }

}