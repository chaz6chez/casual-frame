<?php
declare(strict_types=1);

namespace Kernel\Protocols;

use Kernel\Route;
use RuntimeException;
use Closure;

interface RouterInterface {

    /**
     * @param string $method
     * @param array $arguments
     * @return Route
     */
    public static function __callStatic(string $method, array $arguments) : Route;

    /**
     * @param array $method
     * @param string $route
     * @param Closure|array $callback
     * @return Route
     */
    public static function addRoute(array $method, string $route, $callback) : Route;

    /**
     * @param string $route
     * @return Route|null
     */
    public static function getRoute(string $route): ?Route;

    /**
     * @return Route[]
     */
    public static function getRoutes(): array;

    /**
     * @param string $method
     * @param string $route
     * @param callable|null $error
     * @param array|null $params
     * @throws RuntimeException
     * @return mixed
     */
    public static function dispatch(string $method, string $route, ?callable $error = null, ?array $params = null);
}