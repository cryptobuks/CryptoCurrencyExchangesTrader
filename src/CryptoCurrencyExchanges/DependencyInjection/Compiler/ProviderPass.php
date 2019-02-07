<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\DependencyInjection\Compiler;

use Kefzce\CryptoCurrencyExchanges\Provider\ProviderInterface;
use Kefzce\CryptoCurrencyExchanges\Provider\ProviderResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ProviderPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(ProviderResolver::class)) {
            return;
        }

        $resolverService = $container->findDefinition(ProviderResolver::class);
        $availableProviders = array_keys($container->findTaggedServiceIds(ProviderInterface::SERVICE_TAG));

        foreach ($availableProviders as $provider) {
            $resolverService->addMethodCall('addProvider', [new Reference($provider)]);
        }
    }
}
