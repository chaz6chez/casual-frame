<?php
declare(strict_types=1);

namespace Internal\Kernel\Protocols;

interface ListenerInterface {
    public function onBufferDrain();
    public function onBufferFull();
    public function onMessage();

    public function onConnect();
    public function onClose();
    public function onError();
}
