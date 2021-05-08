<?php
declare(strict_types=1);

namespace Kernel;

use Kernel\Protocols\HandlerInterface;
use Kernel\Protocols\ListenerInterface;
use Workerman\Worker;

/**
 * 用于启动workerman进程的适配器
 * @author chaz6chez <250220719@qq.com>
 * @version 1.0.0 2021-05-09
 * @package Kernel
 */
abstract class AbstractProcess extends Worker implements HandlerInterface{

    /**
     * @param string $socket_name
     * @param array $context_option
     * @return $this
     */
    public function setSocketName(string $socket_name, array $context_option = []) : AbstractProcess{
        $this->_socketName = $socket_name;
        if (!isset($context_option['socket']['backlog'])) {
            $context_option['socket']['backlog'] = static::DEFAULT_BACKLOG;
        }
        $this->_context = \stream_context_create($context_option);
        return $this;
    }

    /**
     * @return $this
     */
    public function __invoke() : AbstractProcess
    {
        $this->onWorkerStart  = [$this, 'onStart'];
        $this->onWorkerStop   = [$this, 'onStop'];
        $this->onWorkerReload = [$this, 'onReload'];

        if($this instanceof ListenerInterface){
            $this->onBufferDrain = [$this, 'onBufferDrain'];
            $this->onBufferFull  = [$this, 'onBufferFull'];
            $this->onMessage     = [$this, 'onMessage'];

            $this->onConnect     = [$this, 'onConnect'];
            $this->onClose       = [$this, 'onClose'];
            $this->onError       = [$this, 'onError'];
        }else{
            $this->_socketName = '';
            $this->_context = null;
        }
        return $this;
    }
}