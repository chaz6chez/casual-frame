<?php
declare(strict_types=1);

namespace Kernel\Tests;

use Kernel\Routers\AbstractRouter;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    /**
     * @var BaseInterface
     */
    protected $_base;
    protected $_methods = [];
    protected function setUp(): void
    {
        AbstractRouter::debug();
        parent::setUp();
    }

    public function methodsProvider(): array
    {
        $res = ['Any' => ['any']];
        foreach ($this->_methods as $method){
            $res[strtoupper($method)] = [$method];
        }
        return $res;
    }

    protected function _generateRandomString(
        int $length = 5,
        string $string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
    ) : string
    {
        $randString = '';
        $i = 0;
        do{
            $randString .= $string[mt_rand(0, strlen($string) - 1)];
            $i ++;
        }while($i < $length);
        return $randString;
    }

    protected function _check(string $route, ?string $expected = null){
        foreach ($this->_methods as $method){
            if($this->_base->dispatch($method,$route) !== ($expected ? $expected : $route)){
                return $route;
            }
        }
        return true;
    }
}

interface BaseInterface {
    public function register($method, $route) : string;

    public function dispatch($method, $route) : string;
}
