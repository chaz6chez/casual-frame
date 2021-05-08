<?php
declare(strict_types=1);

namespace Kernel\Protocols;

interface HandlerInterface {
    public function onStart() : void;
    public function onReload() : void;
    public function onStop() : void;
}