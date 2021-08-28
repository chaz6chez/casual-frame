<?php
declare(strict_types=1);

namespace Kernel\Routers;

use Kernel\Middlewares;
use Kernel\Route;
use Throwable;
use RuntimeException;

/**
 * 该路由简单实现了JsonRpc协议的两种方式
 *
 * @method static Route notice(string $route, callable $callback)
 * @method static Route normal(string $route, callable $callback)
 * @method static Route any(string $route, callable $callback)
 *
 * @example Router.php 继承重写可适配如HTTP等其他协议
 * @author chaz6chez <250220719@qq.com>
 * @version 1.1.8 2021-08-26
 * @package Kernel
 */
class RpcRouter extends AbstractRouter {

    protected static $_methods = [
        'notice', 'normal'
    ];

    /**
     * @param string $group
     * @param Route ...$routes
     * @return mixed
     */
    public static function group(string $group, Route ...$routes): array
    {
        return parent::group($group, ...$routes);
    }

    /**
     * @inheritDoc
     */
    public static function __callStatic(string $method, array $arguments) : Route
    {
        [$route, $callback] = $arguments;
        if (in_array($method = strtolower($method), self::$_methods)) {
            return self::addRoute([$method], $route, $callback);
        } else {
            return self::addRoute(self::$_methods, $route, $callback);
        }
    }

    /**
     * @inheritDoc
     * @return mixed
     */
    public static function dispatch(string $method, string $route, ?callable $error = null, ?array $params = null)
    {
        $route = self::getRoute(self::$_group ? self::$_group . $route : $route);
        if (!$route or !$hasMethod = $route->hasMethod($method)) {
            if($error){
                try {
                    return call_user_func($error);
                }catch (Throwable $throwable){
                    throw new RuntimeException('Error Callback Exception',500, $throwable);
                }
            }
            if(isset($hasMethod)){
                throw new RuntimeException('Forbidden', 403);
            }
            throw new RuntimeException('Not Found',404);
        }
        if(self::$_debug){
            return $params ? self::returnDebug($route, ...$params) : self::returnDebug($route);
        }
        try {
            $callback = Middlewares::run($route->getMiddlewares(), $route->getCallback());
            return $params ? $callback(...$params) : $callback(...$route->getCallback());
        }catch (Throwable $throwable){
            throw new RuntimeException('Dispatch Callback Exception',500, $throwable);
        }
    }
}