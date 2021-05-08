<?php
declare(strict_types=1);

namespace Internal\Kernel\Exceptions;

use Psr\Container\ContainerExceptionInterface;

class ContainerException extends \RuntimeException implements ContainerExceptionInterface {

}