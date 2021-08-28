<?php
declare(strict_types=1);

namespace Kernel\Routers;

use Kernel\Protocols\RouterInterface;
use Kernel\Route;

abstract class AbstractRouter implements RouterInterface {

    /**
     * @var null|string
     */
    protected static $_group;
    /**
     * @var Route[]
     */
    protected static $_routes = [];

    /**
     * @var bool
     */
    protected static $_debug = false;

    /**
     * @param bool $debug
     */
    final public static function debug(bool $debug = true): void
    {
        self::$_debug = $debug;
    }

    /**
     * @param string $group
     * @param Route ...$routes
     * @return Route[]
     */
    public static function group(string $group, Route ...$routes): array
    {
        self::$_group = $group;
        foreach ($routes as $route){
            if(!self::getRoute($routeName = self::$_group . $route->getName())){
                self::addRoute(
                    $route->getMethods(),
                    $routeName,
                    $route->getCallback()
                )->middlewares($route->getMiddlewares());
            }
        }
        self::$_group = null;
        return $routes;
    }

    /**
     * @param Route $route
     * @param mixed ...$params
     * @return string
     */
    final public static function returnDebug(Route $route, ...$params): string
    {
        return $params ? $route->getName() . '?' . implode('&',$params) : $route->getName();
    }

    /**
     * 增加路由
     * @param array $method
     * @param string $route
     * @param callable $callback
     * @return Route
     */
    public static function addRoute(array $method, string $route, callable $callback): Route
    {
        $route = strtolower($route);
        foreach ($method as &$value){
            $value = strtoupper($value);
        }
        self::$_routes[$route] = new Route($method, $route, $callback);
        return self::$_routes[$route];
    }

    /**
     * 获取路由
     * @param string $route
     * @return Route|null
     */
    final public static function getRoute(string $route): ?Route
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
     * @return Route[]
     */
    final public static function getRoutes(): array
    {
        return self::$_routes;
    }
}