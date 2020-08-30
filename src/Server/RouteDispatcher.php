<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Server;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RequestInterface;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RouteDispatcherInterface;
use IliaKologrivov\LaravelJsonRpcServer\Contract\RouteInterface;
use IliaKologrivov\LaravelJsonRpcServer\Exception\InternalErrorException;
use IliaKologrivov\LaravelJsonRpcServer\Exception\InvalidParamsException;
use IliaKologrivov\LaravelJsonRpcServer\Exception\ValidationFailedException;

final class RouteDispatcher implements RouteDispatcherInterface
{

    /**
     * @var Container
     */
    private $container;

    /**
     * @var FormRequestFactory
     */
    private $formRequestFactory;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->formRequestFactory = new FormRequestFactory($container);
    }

    /**
     * @param  RouteInterface  $route
     * @param  RequestInterface  $request
     * @return mixed
     * @throws BindingResolutionException
     */
    public function dispatch(RouteInterface $route, RequestInterface $request)
    {
        if ($route->isControllerAction()) {
            return $this->dispatchController($route->getController(), $route->getAction(), $request);
        }

        return $this->dispatchClosure($route->getAction(), $request);
    }

    private function dispatchClosure($function, RequestInterface $request)
    {
        $args = $this->resolveClassMethodDependencies((new ReflectionFunction($function)), $request);

        return $function(...array_values($args));
    }

    private function dispatchController(string $controller, string $method, RequestInterface $request)
    {
        $controller = $this->container->make($controller);

        try {
            $action = new ReflectionMethod($controller, $method);
        } catch (ReflectionException $exception) {
            throw new InternalErrorException(
                'Method not implemented',
                $exception->getCode(),
                $exception
            );
        }

        $args = $this->resolveClassMethodDependencies($action, $request);

        if (method_exists($controller, 'callAction')) {
            return $controller->callAction($method, $args);
        }

        return $controller->{$method}(...array_values($args));
    }

    private function resolveClassMethodDependencies(ReflectionFunctionAbstract $method, RequestInterface $request)
    {
        $requestParams = $request->getParams();
        $params = $requestParams->all();
        $areParamsNamed = $requestParams->areParamsNamed();

        $args = [];

        foreach ($method->getParameters() as $parameter) {
            /**
             * @var ReflectionClass $class
             */
            $class = $parameter->getClass();

            if ($class) {
                if ($class->isSubclassOf(FormRequest::class)) {
                    $args[] = $this->makeFormRequest($class->name, $request->getParams());
                } elseif ($class->implementsInterface(RequestInterface::class)) {
                    $args[] = $request;
                } else {
                    $args[] = $this->container->make($class->name);
                }
            } else {
                if (! empty($params)) {
                    if ($parameter->isVariadic()) {
                        foreach ($params as $key => $value) {
                            $args[] = $this->cast($value, $parameter);
                        }

                        break;
                    }

                    if ($areParamsNamed) {
                        $name = $parameter->getName();
                        if (array_key_exists($name, $params)) {
                            $args[] = $this->cast($params[$name], $parameter);

                            unset($params[$name]);

                            continue;
                        }
                    } elseif (count($params)) {
                        $args[] = $this->cast(array_shift($params), $parameter);

                        continue;
                    }
                }

                try {
                    $args[] = $parameter->getDefaultValue();
                } catch (ReflectionException $exception) {
                    throw new InvalidParamsException("'{$parameter->getName()}' is required", 0, $exception);
                }
            }
        }

        return $args;
    }

    private function cast($value, ReflectionParameter $parameter)
    {
        $type = $parameter->getType();

        if ($type && $type->isBuiltin()) {
            if ($value === null && $type->allowsNull()) {
                return null;
            }

            $parameterType = $type->getName();

            $valueType = gettype($value);
            if ($valueType === $parameterType) {
                return $value;
            }

            try {
                settype($value, $parameterType);
            } catch (Exception $e) {
                throw new InvalidParamsException("\"{$parameter->getName()}\" type mismatch: cannot cast {$valueType} to {$parameterType}", 0, $e);
            }
        }

        return $value;
    }

    /**
     * @param string $formRequestClass
     * @param RequestParams|null $requestParams
     * @return FormRequest
     */
    private function makeFormRequest(string $formRequestClass, RequestParams $requestParams = null): FormRequest
    {
        $formRequest = $this->formRequestFactory->makeFormRequest($formRequestClass, $requestParams);
        $validator = $this->formRequestFactory->makeValidator($formRequest);

        if ($validator->fails()) {
            throw new ValidationFailedException($validator);
        }

        return $formRequest;
    }

}
