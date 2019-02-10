<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class CommandPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('shell.console')) {
            return;
        }

        $service = $container->findDefinition('shell.console');
        $availableCommands = array_keys($container->findTaggedServiceIds('console.command'));

        foreach ($availableCommands as $command) {
            $service->addMethodCall('add', [new Reference($command)]);
        }
    }
}
