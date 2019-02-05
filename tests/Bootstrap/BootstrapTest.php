<?php

declare(strict_types=1);

namespace App\Tests\Bootstrap;

use App\Bootstrap;
use App\DependencyInjection\Extension\CryptoCurrencyExchangesExtension;
use App\Environment;
use function App\removeDirectory;
use PHPUnit\Framework\TestCase;

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
        $bootstrap->addExtension(new CryptoCurrencyExchangesExtension());
        $bootstrap->addParameters(['qwerty' => 1]);
        /** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
        $container = $bootstrap->boot();
        $this->assertTrue($container->hasParameter('qwerty'));
        $this->assertEquals(1, $container->getParameter('qwerty'));
        $this->assertEquals((string) Environment::dev(), $container->getParameter('ccet.environment'));
    }
}
