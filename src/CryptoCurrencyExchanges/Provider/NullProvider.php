<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider;

final class NullProvider implements ProviderInterface
{
    public function __toString(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    /**
     * @return string
     */
    public function describe(): string
    {
        return '';
    }
}
