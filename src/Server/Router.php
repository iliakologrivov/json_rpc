<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Server;

use IliaKologrivov\LaravelJsonRpcServer\Contract\RouteInterface as RouteContract;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RouteRegistryInterface;
use IliaKologrivov\LaravelJsonRpcServer\Exception\RouteNotFoundException;
use Illuminate\Contracts\Routing\Registrar;

final class Router implements RouteRegistryInterface
{
    /**
     * @var string
     */
    private $currentEndpoint = '';

    private $routes = [];

    private $namespace = '';

    /**
     * @var Registrar
     */
    private $laravelRoute;

    public function __construct(Registrar $laravelRoute)
    {
        $this->laravelRoute = $laravelRoute;
    }

    public function setEndpoint(string $endPoint)
    {
        $this->currentEndpoint = mb_strtolower(trim($endPoint, '/'));

        return $this;
    }

    public function endPoint(string $endPoint, callable $callback)
    {
        return $this->group([
            'endpoint' => $endPoint,
        ], $callback);
    }

    public function namespace(string $value)
    {
        $this->namespace = $value;

        return $this;
    }

    public function method(string $method, $action)
    {
        if (empty($action)) {
            throw new \LogicException("Route for [{$method}] has no action.");
        }

        $this->routes[$this->currentEndpoint][strtolower($method)] = $this->parseAction($action);

        $this->laravelRoute->post($this->currentEndpoint, [Controller::class, 'run']);

        return $this;
    }

    private function parseAction($action)
    {
       if (is_string($action)) {
           $action = explode('@', $action,2);

           return [
               $this->prependGroupNamespace($action[0]),
               $action[1],
           ];
       } elseif (is_callable($action, true)) {
           return [
               $action[0],
               $action[1],
           ];
       } elseif (!empty($action['uses'])) {
            return $this->parseAction($action['uses']);
        }

        return $action;
    }

    private function prependGroupNamespace($class)
    {
        return !empty($this->namespace) && strpos($class, '\\') !== 0
            ? $this->namespace . '\\' . $class : $class;
    }

    public function group(array $attributes, $callback):RouteRegistryInterface
    {
        $oldNamespace = $this->namespace;
        $oldEndpoint = $this->currentEndpoint;

        if (array_key_exists('namespace', $attributes)) {
            $namespace = !empty($this->namespace) && strpos($attributes['namespace'], '\\') !== 0
                ? trim($this->namespace, '\\') . '\\' . trim($attributes['namespace'], '\\')
                : trim($attributes['namespace'], '\\');

            $this->namespace($namespace);
        }

        if (array_key_exists('endpoint', $attributes)) {
            $attributes['endpoint'] = $this->currentEndpoint . '/' . mb_strtolower(trim($attributes['endpoint'], '/'));

            $this->setEndpoint($attributes['endpoint']);
        }

        if ($callback instanceof \Closure) {
            $callback($this);
        } else {
            $router = $this;

            require $callback;
        }

        $this->namespace($oldNamespace);
        $this->setEndpoint($oldEndpoint);

        return $this;
    }

    /**
     * @param string $method
     * @return RouteContract
     */
    public function resolve(string $method): RouteContract
    {
        return $this->findRoute($method);
    }

    /**
     * @param $method
     * @return Route
     */
    private function findRoute(string $method): Route
    {
        $method = strtolower($method);

        if ($this->routes[$this->currentEndpoint][$method]) {
            return new Route($this->routes[$this->currentEndpoint][$method][0], $this->routes[$this->currentEndpoint][$method][1]);
        }

        throw new RouteNotFoundException($method);
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
