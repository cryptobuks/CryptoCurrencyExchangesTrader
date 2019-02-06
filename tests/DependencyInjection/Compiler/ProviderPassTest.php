<?php

declare(strict_types=1);

namespace App\Tests\DependencyInjection\Compiler;

use App\DependencyInjection\Compiler\ProviderPass;
use App\Provider\ProviderInterface;
use App\Provider\ProviderResolver;
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
