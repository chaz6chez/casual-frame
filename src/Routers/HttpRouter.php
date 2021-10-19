<?php
declare(strict_types=1);

namespace Kernel\Routers;

use Kernel\Middlewares;
use Kernel\Route;
use Throwable;
use RuntimeException;

/**
 * 该路由简单实现了Http协议的路由
 *
 * @method static Route get(string $route, callable $callback)
 * @method static Route post(string $route, callable $callback)
 * @method static Route put(string $route, callable $callback)
 * @method static Route patch(string $route, callable $callback)
 * @method static Route delete(string $route, callable $callback)
 * @method static Route options(string $route, callable $callback)
 * @method static Route head(string $route, callable $callback)
 * @method static Route any(string $route, callable $callback)
 *
 * @author chaz6chez <250220719@qq.com>
 * @version 1.1.8 2021-08-26
 * @package Kernel
 */
class HttpRouter extends AbstractRouter {

    /**
     * @var string[]
     */
    protected static $_methods = [
        'get', 'post', 'put', 'patch', 'delete', 'options', 'head'
    ];

    /**
     * @inheritDoc
     */
    public static function __callStatic(string $method, array $arguments) : Route
    {
        [$route, $callback] = $arguments;
        $route = $route[0] !== '/' ? "/{$route}" : $route;
        if (in_array($method = strtolower($method), self::$_methods)) {
            return self::addRoute([$method], $route, $callback);
        } else {
            return self::addRoute(self::$_methods, $route, $callback);
        }
    }

    /**
     * @inheritDoc
     */
    public static function group(?string $group, Route ...$routes): AbstractRouter
    {
        if($group){
            $group = $group[0] !== '/' ? "/{$group}" : $group;
        }
        return parent::group($group, ...$routes);
    }

    /**
     * @inheritDoc
     * @return mixed
     */
    public static function dispatch(string $method, string $route, ?callable $error = null, ?array $params = null)
    {
        $routeName = $route[0] !== '/' ? "/{$route}" : $route;
        $routeName = self::$_group ? self::$_group . $routeName : $routeName;
        $route = self::getRoute($routeName);
        if (!$route or !$hasMethod = $route->hasMethod($method)) {
            if(!isset($hasMethod)){
                $routes = self::getRoutes();
                foreach ($routes as $route){
                    if (preg_match('/\{.*?\}/', $name = $route->getName())) {
                        $paramsIndexArray = [];
                        foreach (explode('/', $name) as $k => $v) {
                            if (preg_match('/\{.*?\}/', $v)) {
                                $paramsIndexArray[] = $k;
                            }
                        }
                        $name = preg_replace('/\{.*?\}/', '[^/]+', $name);
                        if (preg_match('#^' . $name . '$#', $routeName, $matched)) {
                            foreach (explode('/', $matched[0]) as $k => $v) {
                                if (in_array($k, $paramsIndexArray)) {
                                    $params[] = $v;
                                }
                            }
                            if ($hasMethod = $route->hasMethod($method)){
                                goto success;
                            }
                        }
                    }
                }
            }
            if($error){
                try {
                    return call_user_func($error);
                }catch (Throwable $throwable){
                    throw new RuntimeException(
                        "Error Callback Exception [{$routeName}-{$method}]",
                        500,
                        $throwable
                    );
                }
            }
            if(isset($hasMethod) and !$hasMethod){
                throw new RuntimeException("Forbidden [{$routeName}-{$method}]", 403);
            }
            throw new RuntimeException("Not Found [{$routeName}-{$method}]",404);
        }
        success:
        if(self::$_debug){
            return $params ? self::returnDebug($route, ...$params) : self::returnDebug($route);
        }
        try {
            $callback = $route->getCallback(true);
            $func = Middlewares::run($route->getMiddlewares(), $callback);
            return $params ? $func(...$params) : $func(...$callback);
        }catch (Throwable $throwable){
            throw new RuntimeException(
                "Dispatch Callback Exception [{$routeName}-{$method}]",
                500,
                $throwable
            );
        }
    }
}