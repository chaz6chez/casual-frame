<?php
declare(strict_types=1);

namespace Internal\Kernel\Protocols;

interface MiddlewareInterface {
    public function process(\Closure $next, ...$param);
}