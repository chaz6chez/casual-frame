<?php
declare(strict_types=1);

namespace Kernel\Routers;

use Kernel\Protocols\RouterInterface;
use Kernel\Route;
use Closure;

abstract class AbstractRouter implements RouterInterface {

    /** @var Route[]  */
    protected $_group_routes = [];

    /** @var null|string */
    protected static $_group;

    /** @var Route[]  */
    protected static $_routes = [];

    /** @var bool  */
    protected static $_debug = false;

    /**
     * @param bool $debug
     */
    final public static function debug(bool $debug = true): void
    {
        self::$_debug = $debug;
    }

    /**
     * @param string|null $group
     * @param Route ...$routes
     * @return static
     */
    public static function group(?string $group, Route ...$routes): AbstractRouter
    {
        self::$_group = $group;
        foreach ($routes as &$route){
            if(!self::getRoute($routeName = self::$_group . $route->getName())){
                self::delRoute($route->getName());
                $route = self::addRoute(
                    $route->getMethods(),
                    $routeName,
                    $route->getCallback()
                )->middlewares($route->getMiddlewaresString());
            }
        }
        self::$_group = null;
        return Co()->get(static::class)->setGroupRoute($routes ?? []);
    }

    /**
     * @param array $routes
     * @return $this
     */
    public function setGroupRoute(array $routes) : AbstractRouter
    {
        $this->_group_routes = $routes;
        return $this;
    }

    /**
     * @param string[] $middlewares
     * @param bool $top
     * @param bool $replace
     */
    public function middlewares(array $middlewares, bool $top = false, bool $replace = false): void
    {
        foreach ($this->_group_routes as $route){
            $route->middlewares($middlewares, $top, $replace);
        }
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
     * @param Closure|array $callback
     * @return Route
     */
    public static function addRoute(array $method, string $route, $callback): Route
    {
        foreach ($method as &$value){
            $value = strtoupper($value);
        }
        if(
            !$callback instanceof Closure and
            !is_array($callback)
        ){
            throw new \RuntimeException("Illegal route callback [{$route}]");
        }
        self::$_routes[$route] = new Route($method, $route, $callback);
        return self::$_routes[$route];
    }

    /**
     * @param string $route
     */
    public static function delRoute(string $route): void
    {
        if(isset(self::$_routes[$route])){
            unset(self::$_routes[$route]);
        }
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