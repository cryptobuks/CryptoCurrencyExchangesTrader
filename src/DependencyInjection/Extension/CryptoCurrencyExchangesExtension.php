<?php

declare(strict_types=1);

namespace App\DependencyInjection\Extension;

use App\Provider\ProviderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class CryptoCurrencyExchangesExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator());
        $loader->load(__DIR__ . '/../services.yaml');

        foreach ($configs as $key => $value) {
            $container->setParameter($key, $value);
        }

        $container
            ->registerForAutoconfiguration(ProviderInterface::class)
            ->addTag(ProviderInterface::SERVICE_TAG);

        $container->registerForAutoconfiguration(CommandInterface::class)
            ->addTag('console.command');
    }
}
