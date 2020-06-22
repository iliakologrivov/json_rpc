<?php

declare(strict_types=1);

namespace IliaKologrivov\LaravelJsonRpcServer\Server;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use ReflectionClass;
use \ReflectionException;
use ReflectionMethod;
use ReflectionParameter;
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
        $controllerClass = $route->getControllerClass();

        $controller = $this->container->make($controllerClass);

        try {
            $method = new ReflectionMethod($controller, $route->getActionName());
        } catch (ReflectionException $e) {
            throw new InternalErrorException('Method not implemented', $e->getCode(), $e);
        }

        return $this->executeMethod($controller, $method, $request);
    }

    /**
     * @param  object  $controller
     * @param  ReflectionMethod  $method
     * @param  RequestInterface  $request
     * @return mixed
     * @throws BindingResolutionException
     */
    private function executeMethod($controller, ReflectionMethod $method, RequestInterface $request)
    {
        $params = null;
        $areParamsNamed = null;
        $requestParams = $request->getParams();

        if ($requestParams) {
            $params = $requestParams->getParams() ?: null;
            if ($params) {
                $areParamsNamed = $requestParams->areParamsNamed();
            }
        }

        $args = [];

        foreach ($method->getParameters() as $parameter) {
            /**
             * @var ReflectionClass $class
             */
            $class = $parameter->getClass();

            if ($class) {
                if ($class->isSubclassOf(FormRequest::class)) {
                    $args[] = $this->makeFormRequest($class->name, $requestParams);
                } elseif ($class->implementsInterface(RequestInterface::class)) {
                    $args[] = $request;
                } else {
                    $args[] = $this->container->make($class->name);
                }
            } else {
                if (null !== $params) {
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
                    } else {
                        if (count($params)) {
                            $args[] = $this->cast(array_shift($params), $parameter);
                            continue;
                        }
                    }
                }

                try {
                    $args[] = $parameter->getDefaultValue();
                } catch (ReflectionException $e) {
                    throw new InvalidParamsException("'{$parameter->getName()}' is required", 0, $e);
                }
            }
        }

        return $method->invokeArgs($controller, $args);
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
