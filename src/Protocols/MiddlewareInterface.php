<?php
declare(strict_types=1);

namespace Kernel\Protocols;

interface MiddlewareInterface {
    public function process(\Closure $next, ...$param);
}