<?php
declare(strict_types=1);

namespace Kernel\Protocols;

/**
 * 监听事件适配接口
 * Interface ListenerInterface
 * @package Kernel\Protocols
 * @author chaz6chez <250220719@qq.com>
 * @version 1.0.0 2021-05-09
 */
interface ListenerInterface {
    /**
     * @param mixed ...$params
     */
    public function onBufferDrain(...$params) : void;
    /**
     * @param mixed ...$params
     */
    public function onBufferFull(...$params) : void;
    /**
     * @param mixed ...$params
     */
    public function onMessage(...$params) : void;

    /**
     * @param mixed ...$params
     */
    public function onConnect(...$params) : void;
    /**
     * @param mixed ...$params
     */
    public function onClose(...$params) : void;
    /**
     * @param mixed ...$params
     */
    public function onError(...$params) : void;
}
