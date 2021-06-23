<?php
declare(strict_types=1);

namespace Kernel;

/**
 * 该路由简单实现了JsonRpc协议的两种方式
 *
 * @method static Route notice(string $route, callable $callback)
 * @method static Route normal(string $route, callable $callback)
 * @method static Route any(string $route, callable $callback)
 *
 * @example Router.php 33 42 继承重写可适配如HTTP等其他协议
 * @author chaz6chez <250220719@qq.com>
 * @version 1.0.0 2021-05-09
 * @package Kernel
 */
class Router
{
    /**
     * @var Route[]
     */
    protected static $_routes = [];

    /**
     * 静态调用
     * @param string $method
     * @param array $arguments
     * @return Route
     */
    public static function __callStatic(string $method, array $arguments) : Route
    {
        [$route, $callback] = $arguments;
        if (($method = strtolower($method)) === 'any') {
            return self::addRoute(['notice', 'normal'], $route, $callback);
        } else {
            return self::addRoute($method, $route, $callback);
        }
    }

    /**
     * 增加路由
     * @param string|array $method
     * @param string $route
     * @param callable $callback
     * @return Route
     */
    public static function addRoute($method, string $route, callable $callback): Route
    {
        self::$_routes[$route] = new Route($method, $route, $callback);
        return self::$_routes[$route];
    }

    /**
     * 获取路由
     * @param string $route
     * @return Route|null
     */
    public static function getRoute(string $route): ?Route
    {
        if (
            isset(self::$_routes[$route]) and
            self::$_routes[$route] instanceof Route
        ) {
            return self::$_routes[$route];
        }
        return null;
    }

    /**
     * 派遣执行
     * @param string $method
     * @param string $route
     * @param callable|null $error
     * @return false|mixed
     */
    public static function dispatch(string $method, string $route, ?callable $error = null)
    {
        $route = self::getRoute($route);
        if (!$route or !$route->hasMethods($method)) {
            if($error){
                try {
                    return call_user_func($error);
                }catch (\Throwable $throwable){
                    throw new \RuntimeException('Error Callback Exception',500, $throwable);
                }
            }
            throw new \RuntimeException('Not Found',404);
        }

        try {
            return call_user_func(Middlewares::run($route->getMiddlewares(), $route->getCallback()));
        }catch (\Throwable $throwable){
            throw new \RuntimeException('Dispatch Callback Exception',500, $throwable);
        }
    }
}