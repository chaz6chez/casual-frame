<?php
declare(strict_types=1);

namespace Kernel;

use Kernel\Protocols\MiddlewareInterface;

/**
 * Class Route
 * @author chaz6chez <250220719@qq.com>
 * @version 1.1.8 2021-08-26
 * @package Kernel
 */
class Route
{
    /**
     * @var string
     */
    protected $_name = null;

    /**
     * @var string[]
     */
    protected $_methods = [];

    /**
     * @var callable
     */
    protected $_callback = '';

    /**
     * @var Middlewares
     */
    protected $_middlewares;

    /**
     * Route constructor.
     * @param array $methods
     * @param string $name
     * @param callable $callback
     */
    public function __construct(array $methods, string $name, callable $callback)
    {
        $this->setMethods($methods);
        $this->setName($name);
        $this->setCallback($callback);
        /** @var Middlewares _middlewares */
        $this->_middlewares = Co()->get(Middlewares::class);
    }

    /**
     * @return mixed|null
     */
    public function getName() : ?string
    {
        return $this->_name ?? null;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->_name = $name;
    }

    /**
     * @return array
     */
    public function getMethods() : array
    {
        return $this->_methods;
    }

    /**
     * @param array $methods
     */
    public function setMethods(array $methods) : void
    {
        $this->_methods = $methods;
    }

    /**
     * @param string $method
     * @return bool
     */
    public function hasMethod(string $method) : bool
    {
        return boolval(in_array(strtoupper($method), $this->_methods));
    }

    /**
     * @return callable
     */
    public function getCallback() : callable
    {
        return $this->_callback;
    }

    /**
     * @param callable $callback
     */
    public function setCallback(callable $callback) : void
    {
        $this->_callback = $callback;
    }

    /**
     * @return callable[]
     */
    public function getMiddlewares() : array
    {
        return $this->_middlewares->get($this->getName(), true);
    }

    /**
     * @param string $middleware
     * @param bool $top
     * @return $this
     */
    public function middleware(string $middleware, bool $top = false) : Route
    {
        try{
            $middleware = Co()->get($middleware);
            if($middleware instanceof MiddlewareInterface){
                if($top){
                    $this->_middlewares->unshift($this->getName(), $middleware);
                }else{
                    $this->_middlewares->set($this->getName(), $middleware);
                }
            }
        }catch (\Throwable $throwable){
            //todo
        } finally {
            return $this;
        }
    }

    /**
     * @param string[] $middlewares
     * @param bool $top
     * @return $this
     */
    public function middlewares(array $middlewares, bool $top = false) : Route
    {
        foreach ($middlewares as $middleware){
            $this->middleware($middleware, $top);
        }
        return $this;
    }
}
