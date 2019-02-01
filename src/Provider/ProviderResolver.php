<?php

declare(strict_types=1);

namespace App\Provider;

final class ProviderResolver
{
    /** @var array */
    private $providers = [];

    /**
     * @param \App\Provider\ProviderInterface $provider
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
