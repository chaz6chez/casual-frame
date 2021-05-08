<?php
declare(strict_types=1);

namespace Internal\Kernel;

use Internal\Kernel\Protocols\MiddlewareInterface;
use Closure;

/**
 * 中间件管理器
 *  用于收集中间件(实现MiddlewareInterface的类)
 * Class Middlewares
 * @package Internal\Kernel
 */
class Middlewares
{
    /**
     * @var MiddlewareInterface[][]
     */
    protected $_middlewares = [];

    /**
     * @param string $name
     * @param MiddlewareInterface $middleware
     */
    public function set(string $name, MiddlewareInterface $middleware)
    {
        $this->_middlewares[$name][] = [$middleware, 'process'];
    }

    /**
     * @param string $name
     * @param MiddlewareInterface[] $middlewares
     */
    public function load(string $name, array $middlewares)
    {
        foreach ($middlewares as $middleware) {
            if(
                is_object($middleware) and
                $middleware instanceof MiddlewareInterface
            ){
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
        $res = isset($this->_middlewares[$name]) ? $this->_middlewares[$name] : [];
        if($base){
            $res = array_merge(isset($this->_middlewares['@base']) ? $this->_middlewares['@base'] : [], $res);
        }
        return \array_reverse($res);
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
     * 中间件调用程序
     * @param callable[] $callables
     * @param callable $init
     * @param mixed ...$param
     * @return Closure
     */
    public static function run(array $callables, callable $init, ...$param) : Closure{
        return array_reduce($callables, function ($carry, $pipe) {
            return function (...$param) use ($carry, $pipe) {
                return $pipe($carry, ...$param);
            };
        }, function () use ($init, $param) {
            $init(...$param);
        });
    }
}