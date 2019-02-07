<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider;

interface Configurable
{
    /**
     * Any array of configuration, that you need to provide into the service to change default behaviour
     * e.g some argument input, used like a method injection.
     *
     * @param array|null $config
     */
    public function provide(array $config = []): void;
}
