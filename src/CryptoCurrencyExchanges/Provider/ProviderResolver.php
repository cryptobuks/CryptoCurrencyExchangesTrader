<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider;

final class ProviderResolver
{
    /** @var array|ProviderInterface[] */
    private $providers = [];

    /**
     * @param ProviderInterface $provider
     */
    public function addProvider(ProviderInterface $provider): void
    {
        if (\in_array($provider, $this->providers, true)) {
            return;
        }

        $this->providers[] = $provider;
    }

    /**
     * @return array
     */
    public function getProviders(): array
    {
        return $this->providers;
    }
}
