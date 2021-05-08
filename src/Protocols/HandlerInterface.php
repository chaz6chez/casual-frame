<?php
declare(strict_types=1);

namespace Kernel\Protocols;

/**
 * 进程事件适配接口
 * Interface HandlerInterface
 * @package Kernel\Protocols
 * @author chaz6chez <250220719@qq.com>
 * @version 1.0.0 2021-05-09
 */
interface HandlerInterface {
    /**
     * @param mixed ...$param
     */
    public function onStart(...$param) : void;
    /**
     * @param mixed ...$param
     */
    public function onReload(...$param) : void;
    /**
     * @param mixed ...$param
     */
    public function onStop(...$param) : void;
}