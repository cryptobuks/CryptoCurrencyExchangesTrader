<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Tests\DependencyInjection\ContainerBuilder;

use Kefzce\CryptoCurrencyExchanges\DependencyInjection\Compiler\CommandPass;
use Kefzce\CryptoCurrencyExchanges\DependencyInjection\Compiler\ProviderPass;
use Kefzce\CryptoCurrencyExchanges\DependencyInjection\ContainerBuilder\ContainerBuilder;
use Kefzce\CryptoCurrencyExchanges\DependencyInjection\Extension\KefzceCryptoCurrencyExchangesExtension;
use Kefzce\CryptoCurrencyExchanges\Environment;
use function Kefzce\CryptoCurrencyExchanges\removeDirectory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
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
     * @covers \Kefzce\CryptoCurrencyExchanges\DependencyInjection\ContainerBuilder\ContainerBuilder
     */
    public function successfulBuild(): void
    {
        $containerBuilder = new ContainerBuilder(Environment::test());

        $containerBuilder->setCacheDirectoryPath($this->cacheDirectory);

        $containerBuilder->addParameters(['zas' => 123]);

        $this->assertFalse($containerBuilder->hasActualContainer());

        /** @var ContainerInterface $container */
        $container = $containerBuilder->build();

        $this->assertFileExists(sys_get_temp_dir() . '/container_test');

        $this->assertSame(123, $container->getParameter('zas'));

        @unlink(sys_get_temp_dir() . '/container_test');
        removeDirectory(sys_get_temp_dir() . '/container_test');
    }

    /**
     * @test
     * @covers \Kefzce\CryptoCurrencyExchanges\DependencyInjection\ContainerBuilder\ContainerBuilder
     */
    public function successfulBuildWithFullConfiguration(): void
    {
        removeDirectory($this->cacheDirectory);
        $containerBuilder = new ContainerBuilder(Environment::boot());

        $this->assertFalse($containerBuilder->hasActualContainer());

        $containerBuilder->addCompilerPasses(new CommandPass(), new ProviderPass());
        $containerBuilder->addExtensions(new KefzceCryptoCurrencyExchangesExtension());
        $containerBuilder->setCacheDirectoryPath($this->cacheDirectory);
        $containerBuilder->addParameters([
            'testz' => 123,
            'class' => \get_class($this),
        ]);

        /** @var ContainerInterface $c */
        $c = $containerBuilder->build();
        $this->assertTrue($c->hasParameter('testz'));
        $this->assertTrue($c->hasParameter('class'));
        $this->assertEquals(\get_class($this), $c->getParameter('class'));
        $this->assertEquals(123, $c->getParameter('testz'));

        $this->assertInstanceOf(ContainerInterface::class, $c);

        $r = new ReflectionClass($containerBuilder);
        $property = $r->getProperty('environment');
        $property->setAccessible(true);
        $this->assertSame((string) Environment::boot(), (string) $property->getValue($containerBuilder));
        @unlink(sys_get_temp_dir() . '/container_test');
        removeDirectory(sys_get_temp_dir() . '/container_test');
    }
}
