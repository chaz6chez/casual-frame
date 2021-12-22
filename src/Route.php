<?php
declare(strict_types=1);

namespace Kernel;

use Kernel\Protocols\MiddlewareInterface;
use Closure;

/**
 * Class Route
 * @author chaz6chez <250220719@qq.com>
 * @version 1.1.8 2021-08-26
 * @package Kernel
 */
class Route
{
    /** @var string */
    protected $_name;

    /** @var string[] */
    protected $_methods = [];

    /** @var callable|array */
    protected $_callback;

    /** @var Middlewares  */
    protected $_middlewares;

    /**
     * Route constructor.
     * @param array $methods
     * @param string $name
     * @param Closure|array $callback
     */
    public function __construct(array $methods, string $name, $callback)
    {
        $this->setMethods($methods);
        $this->setName($name);
        if(
            !$callback instanceof Closure and
            !is_array($callback)
        ){
            throw new \RuntimeException("Illegal route callback [{$name}]");
        }
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
     * @param bool $make
     * @return null|Closure|array
     */
    public function getCallback(bool $make = false)
    {
        if($make and !$this->_callback instanceof Closure){
            [$class, $method] = $this->_callback;
            return [make($class), $method];
        }
        return $this->_callback;
    }

    /**
     * @param Closure|array $callback
     */
    public function setCallback($callback) : void
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
     * @return String[]
     */
    public function getMiddlewaresString() : array
    {
        $middlewares = $this->_middlewares->get($this->getName(), true);
        foreach ($middlewares as &$middleware){
            if([$middleware,] = $middleware){
                $middleware = get_class($middleware);
            }
        }
        return $middlewares;
    }

    /**
     * @param string $middleware
     * @param bool $top
     * @param bool $replace
     * @return $this
     */
    public function middleware(string $middleware, bool $top = false, bool $replace = false) : Route
    {
        try{
            $middleware = Co()->get($middleware);
            if($middleware instanceof MiddlewareInterface){
                if($top){
                    $this->_middlewares->unshift($this->getName(), $middleware, $replace);
                }else{
                    $this->_middlewares->set($this->getName(), $middleware, $replace);
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
     * @param bool $replace
     * @return $this
     */
    public function middlewares(array $middlewares, bool $top = false, bool $replace = false) : Route
    {
        foreach ($middlewares as $middleware){
            $this->middleware($middleware, $top, $replace);
        }
        return $this;
    }
}
