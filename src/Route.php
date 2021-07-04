<?php
declare(strict_types=1);

namespace Kernel;

use Kernel\Protocols\MiddlewareInterface;

/**
 * Class Route
 * @author chaz6chez <250220719@qq.com>
 * @version 1.0.0 2021-05-09
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
     * @param $methods
     * @param string $name
     * @param callable $callback
     */
    public function __construct($methods, string $name, callable $callback)
    {
        $this->_methods = (array) $methods;
        $this->_name = $name;
        $this->_callback = $callback;
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

    /**
     * @return array
     */
    public function getMethods() : array
    {
        return $this->_methods;
    }

    public function hasMethods(string $method) : bool
    {
        return boolval(in_array($method,$this->_methods));
    }

    /**
     * @return callable
     */
    public function getCallback() : callable
    {
        return $this->_callback;
    }

    /**
     * @return callable[]
     */
    public function getMiddlewares() : array
    {
        return $this->_middlewares->get($this->getName(), true);
    }
}
