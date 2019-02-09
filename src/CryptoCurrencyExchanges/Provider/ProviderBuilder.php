<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

final class ProviderBuilder implements ProviderBuilderInterface
{
    /**
     * @var ContainerInterface
     */
    private static $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        self::$container = $container;
    }

    /**
     * @param string $className
     *
     * @return ProviderInterface
     */
    public function build(string $className)
    {
        if (null === self::$container) {
            return;
        }

        if (!self::$container->has($className)) {
            throw new ServiceNotFoundException($className);
        }

        return self::$container->get($className);
    }
}
