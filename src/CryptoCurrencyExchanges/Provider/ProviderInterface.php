<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider;

interface ProviderInterface
{
    /**
     * All providers in DependencyInjection Container can be found by this service tag.
     */
    public const SERVICE_TAG = 'exchanges.provider';

    /**
     * Each provider should have a possibility self converted to a string, because they FQCN uses to provide some info.
     *
     * @see {\App\Command\SearchProvidersCommand::execute}
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * @return string An Available provider describe string
     */
    public function describe(): string;
}
