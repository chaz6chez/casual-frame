<?php
declare(strict_types=1);

namespace Kernel\Tests;

use Kernel\Route;
use Kernel\Routers\HttpRouter;

class HttpRouterTest extends BaseTestCase {

    protected $_methods = [
        'get', 'post', 'put', 'patch', 'delete', 'options', 'head'
    ];

    protected function setUp() : void
    {
        $this->_base = new Http();
        parent::setUp();
    }

    /**
     * @dataProvider methodsProvider
     */
    public function testSuccessOfRandomRoute($method)
    {
        $randomString = $this->_generateRandomString(4);
        $this->_base->register($method, "/{$randomString}");
        if($method === 'any'){
            $this->assertEquals(
                 true,
                $this->_check(strtolower("/{$randomString}"))
            );
        }else{
            $this->assertEquals(
                strtolower("/{$randomString}"),
                $this->_base->dispatch($method, strtolower("/{$randomString}"))
            );
        }
    }

    /**
     * @dataProvider methodsProvider
     */
    public function testSuccessOfNoRootPath($method)
    {
        $randomString = $this->_generateRandomString(4);
        $this->_base->register($method, "{$randomString}");
        if($method === 'any'){
            $this->assertEquals(
                 true,
                $this->_check(strtolower("/{$randomString}"))
            );
        }else{
            $this->assertEquals(
                strtolower("/{$randomString}"),
                $this->_base->dispatch($method, strtolower("/{$randomString}"))
            );
        }
    }

    /**
     * @dataProvider methodsProvider
     */
    public function testSuccessOfOneParamFixedRoute($method)
    {
        $randomString = $this->_generateRandomString(4);
        if($method === 'any'){
            $this->_base->register($method, '/demo/{string}');
            $this->assertEquals(
                 true,
                $this->_check("/demo/{$randomString}","/demo/{string}?{$randomString}")
            );
        }else{
            $this->_base->register($method, '/demo/{string}');
            $this->assertEquals(
                "/demo/{string}?{$randomString}",
                $this->_base->dispatch($method, "/demo/{$randomString}")
            );
        }
    }

    /**
     * @dataProvider methodsProvider
     */
    public function testSuccessOfTwoParamsFixedRoute($method)
    {
        $randomString = $this->_generateRandomString(6);
        $randomInt = (int)($this->_generateRandomString(2,'0123456789'));
        if($method === 'any'){
            $this->_base->register($method, '/demo/{string}/user/{id}');
            $this->assertEquals(
                true,
                $this->_check(
                    "/demo/{$randomString}/user/{$randomInt}",
                    "/demo/{string}/user/{id}?{$randomString}&{$randomInt}"
                )
            );
        }else{
            $this->_base->register($method, '/demo/{string}/user/{id}');
            $this->assertEquals(
                "/demo/{string}/user/{id}?{$randomString}&{$randomInt}",
                $this->_base->dispatch($method, "/demo/{$randomString}/user/{$randomInt}")
            );
        }
    }

    /**
     * @dataProvider methodsProvider
     */
    public function test403($method)
    {
        $this->expectExceptionCode(403);
        $this->assertEquals(
            $this->_base->register($method, '/demo'),
            $this->_base->dispatch('unknown', '/demo')
        );
    }

    /**
     * @dataProvider methodsProvider
     */
    public function test404($method)
    {
        $this->expectExceptionCode(404);
        $this->assertEquals(
            $this->_base->register($method, '/demo'),
            $this->_base->dispatch($method, '/demo1')
        );
    }

}

class Http implements BaseInterface {
    public function register($method, $route) : string
    {
        /** @var Route $routeObj */
        $routeObj = HttpRouter::$method($route, function () {});
        return $routeObj->getName();
    }

    public function dispatch($method, $route) : string
    {
        return HttpRouter::dispatch($method, $route);
    }
}