<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Server;

use IliaKologrivov\LaravelJsonRpcServer\Contract\RouteInterface as RouteContract;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RouterInterface;
use IliaKologrivov\LaravelJsonRpcServer\Exception\RouteNotFoundException;
use Illuminate\Contracts\Routing\Registrar;

final class Router implements RouterInterface
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

    public function endpoint(string $endPoint, $callback): RouterInterface
    {
        return $this->group([
            'endpoint' => $endPoint,
        ], $callback);
    }

    public function method(string $method, $action): RouterInterface
    {
        if (empty($action)) {
            throw new \LogicException("Route for [{$method}] has no action.");
        }

        $this->routes[$this->currentEndpoint][strtolower($method)] = $this->parseAction($action);

        $this->laravelRoute->post($this->currentEndpoint, [Controller::class, 'run']);

        return $this;
    }

    public function group(array $attributes, $callback): RouterInterface
    {
        $oldNamespace = $this->namespace;
        $oldEndpoint = $this->currentEndpoint;

        if (array_key_exists('namespace', $attributes)) {
            $this->namespace = $this->prependNamespace($attributes['namespace']);
        }

        if (array_key_exists('endpoint', $attributes)) {
            $this->currentEndpoint = ltrim($this->currentEndpoint . '/' . trim(mb_strtolower($attributes['endpoint']), '/'), '/');
        }

        $this->loadRoutes($callback);

        $this->namespace = $oldNamespace;
        $this->currentEndpoint = $oldEndpoint;

        return $this;
    }

    public function resolve(string $endpoint, string $method): RouteContract
    {
        $method = strtolower($method);

        if ($this->routes[$endpoint][$method] ?? false) {
            return new Route($this->routes[$endpoint][$method][0], $this->routes[$endpoint][$method][1]);
        }

        throw new RouteNotFoundException($method);
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function setRoutes(array $routes): RouterInterface
    {
        $this->routes = $routes;

        return $this;
    }

    private function loadRoutes($routes)
    {
        if ($routes instanceof \Closure) {
            $routes($this);
        } else {
            $router = $this;

            require $routes;
        }
    }

    private function parseAction($action): array
    {
        if (is_string($action)) {
            $action = explode('@', $action,2);

            return [
                $this->prependNamespace($action[0]),
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

    private function prependNamespace(string $class): string
    {
        return !empty($this->namespace) && strpos($class, '\\') !== 0
            ? trim($this->namespace, '\\') . '\\' . trim($class, '\\')
            : trim($class, '\\');
    }

    public function __call($method, $parameters)
    {
        return (new RouteRegistry($this))->attribute($method, ...$parameters);
    }
}
