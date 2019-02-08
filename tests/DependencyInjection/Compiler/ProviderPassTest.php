<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Tests\DependencyInjection\Compiler;

use Kefzce\CryptoCurrencyExchanges\DependencyInjection\Compiler\ProviderPass;
use Kefzce\CryptoCurrencyExchanges\Provider\ProviderInterface;
use Kefzce\CryptoCurrencyExchanges\Provider\ProviderResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ProviderPassTest extends TestCase
{
    public function testProcessConsiderAnProviderPassWasLoadedCorrectly(): void
    {
        $builder = new ContainerBuilder();
        $builder->register(ProviderResolver::class, new Definition(ProviderResolver::class));

        (new ProviderPass())->process($builder);

        $this->assertTrue(
            $builder->hasDefinition(ProviderResolver::class)
        );

        $resolver = $builder->findDefinition(ProviderResolver::class);

        $unusedTags = array_keys($builder->findUnusedTags(ProviderInterface::SERVICE_TAG));
        $this->assertEmpty($unusedTags);
        $this->assertNotEmpty($resolver);
    }
}
