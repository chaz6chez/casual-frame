<?php
declare(strict_types=1);

namespace Kernel\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends \RuntimeException implements NotFoundExceptionInterface {

}