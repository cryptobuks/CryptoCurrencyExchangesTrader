<?php

declare(strict_types=1);

namespace App\Tests\Application\DependencyInjection\ContainerBuilder;

use App\DependencyInjection\Compiler\CommandPass;
use App\DependencyInjection\Compiler\ProviderPass;
use App\DependencyInjection\ContainerBuilder\ContainerBuilder;
use App\DependencyInjection\Extention\CryptoCurrencyExchangesExtention;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContainerBuilderTest extends TestCase
{
    /**
     * @var string
     */
    private $cacheDirectory;

    public function setUp()
    {
        parent::setUp();

        $this->cacheDirectory = sys_get_temp_dir() . '/container_test';

        if (false === file_exists($this->cacheDirectory)) {
            mkdir($this->cacheDirectory);
        }
    }

    public function tearDown()
    {
        parent::tearDown();

        unset($this->cacheDirectory);
    }

    /**
     * @test
     * @covers \App\DependencyInjection\ContainerBuilder\ContainerBuilder
     */
    public function successfulBuild(): void
    {
        $containerBuilder = new ContainerBuilder();

        $this->assertFalse($containerBuilder->hasActualContainer());

        /** @var ContainerInterface $container */
        $container = $containerBuilder->build();

        $this->assertFileExists(sys_get_temp_dir() . '/container_test');

        @unlink(sys_get_temp_dir() . '/container_test');
    }

    /**
     * @test
     * @covers \App\DependencyInjection\ContainerBuilder\ContainerBuilder
     */
    public function successfulBuildWithFullConfiguration(): void
    {
        $containerBuilder = new ContainerBuilder();

        $this->assertFalse($containerBuilder->hasActualContainer());

        $containerBuilder->addCompilerPasses(new CommandPass(), new ProviderPass());
        $containerBuilder->addExtensions(new CryptoCurrencyExchangesExtention());

        /** @var ContainerInterface $container */
        $container = $containerBuilder->build();

        $this->assertInstanceOf(ContainerInterface::class, $container);
    }
}
