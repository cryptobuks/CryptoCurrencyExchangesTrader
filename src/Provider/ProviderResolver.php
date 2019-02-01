<?php

declare(strict_types=1);

namespace App\Provider;

final class ProviderResolver
{
    /** @var \SplObjectStorage */
    private $providers;

    public function __construct()
    {
        $this->providers = new \SplObjectStorage();
    }

    public function addProvider(ProviderInterface $provider)
    {
        if ($this->providers->contains($provider)) {
            return;
        }

        $this->providers->attach($provider);
    }

    public function test(): string
    {
        return 'da suka';
    }
}
