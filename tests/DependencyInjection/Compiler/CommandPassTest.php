<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Tests\DependencyInjection\Compiler;

use Kefzce\CryptoCurrencyExchanges\DependencyInjection\Compiler\CommandPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class CommandPassTest extends TestCase
{
    public function testProcessConsiderAnCommandPassWasLoadedCorrectly(): void
    {
        $builder = new ContainerBuilder();
        $builder->register(Application::class, new Definition(Application::class));

        (new CommandPass())->process($builder);

        $this->assertTrue(
            $builder->hasDefinition(Application::class)
        );

        $application = $builder->findDefinition(Application::class);

        $unusedTags = array_keys($builder->findUnusedTags('console.command'));
        $this->assertEmpty($unusedTags);
        $this->assertNotEmpty($application);
    }
}
