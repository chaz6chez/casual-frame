<?php
declare(strict_types=1);

namespace Kernel;
use Kernel\Exceptions\ContainerException;
use Kernel\Exceptions\NotFoundException;
use Psr\Container\ContainerInterface;

/**
 * 实现了简易的单例容器
 * Class Container
 * @author chaz6chez <250220719@qq.com>
 * @version 1.0.0 2021-05-09
 * @package Kernel
 */
class Container implements ContainerInterface
{
    /** @var Container */
    protected static $_self;

    /** @var object[]  */
    protected $_instances = [];

    /**
     * 长生命周期单例
     * @param string $id
     * @return object
     */
    public function get(string $id) : object
    {
        if (!$this->has($id)) {
            if(!class_exists($id)){
                throw new NotFoundException("Class {$id} Not Found", 404);
            }
            try{
                $this->_instances[$id] = new $id();
            }catch (\Throwable $throwable){
                throw new ContainerException($throwable->getMessage(),$throwable->getCode());
            }
        }
        return $this->_instances[$id];
    }

    /**
     * 判断
     * @param string $id
     * @return bool
     */
    public function has(string $id) : bool
    {
        return boolval(isset($this->_instances[$id]));
    }

    /**
     * 短生命周期
     * @param string $id
     * @param mixed ...$constructor
     * @return object
     */
    public function make(string $id, ...$constructor) : object
    {
        if (!class_exists($id)) {
            throw new NotFoundException("Class {$id} Not Found", 404);
        }
        try{
            return new $id(...$constructor);
        }catch (\Throwable $throwable){
            throw new ContainerException($throwable->getMessage(),$throwable->getCode());
        }
    }

    /**
     * 容器实单例
     * @return Container
     */
    public static function instance() : Container {
        if(!self::$_self instanceof Container){
            self::$_self = new Container();
        }
        return self::$_self;
    }

    /**
     * @return object[]
     */
    public function debug() : array{
        return $this->_instances;
    }

}