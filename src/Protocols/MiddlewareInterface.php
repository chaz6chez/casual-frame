<?php
declare(strict_types=1);

namespace Kernel\Protocols;

/**
 * Interface MiddlewareInterface
 * @package Kernel\Protocols
 * @author chaz6chez <250220719@qq.com>
 * @version 1.0.0 2021-05-09
 */
interface MiddlewareInterface {
    public function process(\Closure $next, ...$param);
}