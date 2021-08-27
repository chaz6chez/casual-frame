<?php
declare(strict_types=1);

namespace Kernel;

use Kernel\Routers\RpcRouter;

/**
 * 该路由简单实现了JsonRpc协议的两种方式
 * @example Router.php 继承重写可适配如HTTP等其他协议
 * @author chaz6chez <250220719@qq.com>
 * @version 1.1.8 2021-08-26
 * @package Kernel
 * @see RpcRouter
 */
class Router extends RpcRouter {}