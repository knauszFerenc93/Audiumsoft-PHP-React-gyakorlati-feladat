<?php

declare(strict_types=1);

namespace ADS\TakeHome\Exceptions;

final class NotFoundException extends ClientException
{
    public function status(): int
    {
        return 404;
    }
}
