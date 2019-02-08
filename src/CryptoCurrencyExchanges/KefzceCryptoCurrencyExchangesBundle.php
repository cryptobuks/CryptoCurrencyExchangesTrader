<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges;

use Kefzce\CryptoCurrencyExchanges\DependencyInjection\Extension\KefzceCryptoCurrencyExchangesExtension;
use Kefzce\CryptoCurrencyExchanges\Provider\ProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class KefzceCryptoCurrencyExchangesBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container
            ->registerForAutoconfiguration(ProviderInterface::class)
            ->addTag(ProviderInterface::SERVICE_TAG);
    }

    public function getContainerExtension()
    {
        return new KefzceCryptoCurrencyExchangesExtension();
    }
}
