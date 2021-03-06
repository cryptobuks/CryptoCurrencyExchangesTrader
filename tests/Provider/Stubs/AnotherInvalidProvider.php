<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Tests\Provider\Stubs;

use ReflectionClass;

final class AnotherInvalidProvider
{
    public function __toString(): string
    {
        $r = new ReflectionClass($this);

        return $r->getShortName();
    }
}
