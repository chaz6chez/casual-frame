<?php
declare(strict_types=1);

namespace Kernel;

use Kernel\Protocols\MiddlewareInterface;
use Closure;

/**
 * 用于收集并管理MiddlewareInterface的中间件管理类
 * @author chaz6chez <250220719@qq.com>
 * @version 1.0.0 2021-05-09
 * @package Kernel
 */
class Middlewares
{
    const BASE = '@base';

    /** @var callable[][]  */
    protected $_middlewares = [];

    /**
     * 设置中间件
     * @param string $name
     * @param MiddlewareInterface $middleware
     * @param bool $replace
     */
    public function set(string $name, MiddlewareInterface $middleware, bool $replace = false) : void
    {
        if($replace and $this->isset($name, $middleware) !== null){
            $this->del($name, $middleware);
        }
        $this->_middlewares[$name][] = [$middleware, 'process'];
    }

    /**
     * @param string $name
     * @param MiddlewareInterface $middleware
     * @param bool $replace
     */
    public function unshift(string $name, MiddlewareInterface $middleware, bool $replace = false) : void
    {
        if(!$this->has($name)){
            $this->set($name, $middleware);
            return;
        }
        if($replace){
            $this->del($name, $middleware);
        }
        array_unshift($this->_middlewares[$name], [$middleware, 'process']);
    }

    /**
     * @param string $project
     */
    public function init(string $project){
        $middlewares = C("middlewares.{$project}", []);
        foreach ($middlewares as $middleware){
            if($middleware instanceof MiddlewareInterface){
                $this->base($middleware);
            }
        }
    }

    /**
     * @param MiddlewareInterface $middleware
     */
    public function base(MiddlewareInterface $middleware) : void
    {
        $this->set(self::BASE, $middleware);
    }

    /**
     * @param string $name
     * @param MiddlewareInterface[] $middlewares
     */
    public function load(string $name, array $middlewares) : void
    {
        foreach ($middlewares as $middleware) {
            if($middleware instanceof MiddlewareInterface){
                $this->set($name, $middleware);
            }
        }
    }

    /**
     * @param string $name
     * @param bool $base
     * @return callable[]
     */
    public function get(string $name, bool $base = false) : array
    {
        $res = $this->has($name) ? $this->_middlewares[$name] : [];
        if($base){
            $res = array_merge(isset($this->_middlewares[self::BASE]) ? $this->_middlewares[self::BASE] : [], $res);
        }
        return \array_reverse($res);
    }

    /**
     * @param string $name
     * @param MiddlewareInterface|null $middleware
     */
    public function del(string $name, ?MiddlewareInterface $middleware = null) : void
    {
        if($this->get($name)){
            $key = $this->isset($name, $middleware);
            if($key !== null){
                unset($this->_middlewares[$name][$key]);
            }else{
                unset($this->_middlewares[$name]);
            }
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name) : bool
    {
        return boolval(isset($this->_middlewares[$name]));
    }

    /**
     * @param string $name
     * @param null|MiddlewareInterface $middleware
     * @return null|int
     */
    public function isset(string $name, ?MiddlewareInterface $middleware) : ?int
    {
        if(
            $this->has($name) and
            $middleware instanceof MiddlewareInterface
        ){
            return array_search([$middleware, 'process'], $this->get($name));
        }
        return null;
    }

    /**
     * 中间件调用程序
     * @param callable[] $callables
     * @param callable $init
     * @return Closure (...$params)
     */
    public static function run(array $callables, callable $init) : Closure
    {
        return array_reduce($callables, function ($carry, $pipe) {
            return function (...$params) use ($carry, $pipe) {
                return $pipe($carry, ...$params);
            };
        }, function (...$params) use ($init) {
            return $init(...$params);
        });
    }
}