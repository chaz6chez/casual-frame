<?php
declare(strict_types=1);

namespace Kernel\Tests;

use Kernel\Route;
use Kernel\Routers\RpcRouter;

class RpcRouterTest extends BaseTestCase {

    protected $_methods = [
        'notice', 'normal'
    ];

    protected function setUp() : void
    {
        $this->_base = new Rpc();
        parent::setUp();
    }

    /**
     * @dataProvider methodsProvider
     */
    public function testSuccess($method)
    {
        $this->_base->register($method, 'demo');
        if($method === 'any'){
            $this->assertEquals(
                 true,
                $this->_check('demo')
            );
        }else{
            $this->assertEquals(
                'demo',
                $this->_base->dispatch($method, 'demo')
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
            $this->_base->register($method, 'demo'),
            $this->_base->dispatch('unknown', 'demo')
        );
    }

    /**
     * @dataProvider methodsProvider
     */
    public function test404($method)
    {
        $this->expectExceptionCode(404);
        $this->assertEquals(
            $this->_base->register($method, 'demo'),
            $this->_base->dispatch($method, 'demo1')
        );
    }
}

class Rpc implements BaseInterface {
    public function register($method, $route):string
    {
        /** @var Route $routeObj */
        $routeObj = RpcRouter::$method($route, function (){});
        return $routeObj->getName();
    }

    public function dispatch($method, $route): string
    {
        return RpcRouter::dispatch($method, $route);
    }
}
