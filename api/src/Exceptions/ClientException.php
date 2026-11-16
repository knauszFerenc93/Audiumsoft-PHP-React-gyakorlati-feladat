<?php

declare(strict_types=1);

namespace ADS\TakeHome\Exceptions;

use RuntimeException;

abstract class ClientException extends RuntimeException
{
    abstract public function status(): int;
}
