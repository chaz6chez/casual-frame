<?php
declare(strict_types=1);

namespace Internal\Kernel;

use Internal\Kernel\Protocols\HandlerInterface;
use Internal\Kernel\Protocols\ListenerInterface;
use Workerman\Worker;

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