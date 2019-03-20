<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Tests\Bootstrap;

use Kefzce\CryptoCurrencyExchanges\Bootstrap;
use Kefzce\CryptoCurrencyExchanges\DependencyInjection\Extension\KefzceCryptoCurrencyExchangesExtension;
use Kefzce\CryptoCurrencyExchanges\Environment;
use function Kefzce\CryptoCurrencyExchanges\removeDirectory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BootstrapTest extends TestCase
{
    private $cacheDirectory;

    public function setUp()
    {
        parent::setUp();
        $this->cacheDirectory = sys_get_temp_dir() . '/bootstrap_test';

        if (false === file_exists($this->cacheDirectory)) {
            mkdir($this->cacheDirectory);
        }
    }

    public function tearDown()
    {
        parent::tearDown();
        removeDirectory($this->cacheDirectory);
    }

    /**
     * @test
     */
    public function withEnvironmentValues(): void
    {
        $bootstrap = Bootstrap::withEnvironmentValues();
        $bootstrap->useCustomCacheDirectory($this->cacheDirectory);
        $bootstrap->addExtension(new KefzceCryptoCurrencyExchangesExtension());
        $bootstrap->addParameters(['qwerty' => 1]);
        /** @var ContainerInterface $container */
        $container = $bootstrap->boot();
        $this->assertTrue($container->hasParameter('qwerty'));
        $this->assertEquals(1, $container->getParameter('qwerty'));
        $this->assertEquals((string) Environment::dev(), $container->getParameter('ccet_environment'));
    }
}
