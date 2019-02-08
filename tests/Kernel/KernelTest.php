<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Tests\Kernel;

use Kefzce\CryptoCurrencyExchanges\Bootstrap;
use Kefzce\CryptoCurrencyExchanges\DependencyInjection\Extension\KefzceCryptoCurrencyExchangesExtension;
use Kefzce\CryptoCurrencyExchanges\Kernel;
use function Kefzce\CryptoCurrencyExchanges\removeDirectory;
use PHPUnit\Framework\TestCase;

class KernelTest extends TestCase
{
    /**
     * @var string
     */
    private $cacheDirectory;

    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    public function setUp()
    {
        parent::setUp();
        $this->cacheDirectory = sys_get_temp_dir() . '/kernel_test/';

        if (false === file_exists($this->cacheDirectory)) {
            mkdir($this->cacheDirectory);
        }

        $bootstrap = Bootstrap::withDotEnv(__DIR__ . '/Stubs');
        $bootstrap->useCustomCacheDirectory($this->cacheDirectory);
        $bootstrap->addExtension(new KefzceCryptoCurrencyExchangesExtension());
        $bootstrap->provideParametersPath(__DIR__ . 'Stubs/parameters.php');

        $bootstrap->addParameters(['std' => new \stdClass()]);

        $this->container = $bootstrap->boot();

        $this->kernel = new Kernel($this->container);
    }

    public function tearDown()
    {
        removeDirectory($this->cacheDirectory);
        parent::tearDown();

        unset($this->kernel,$this->container,$this->cacheDirectory);
    }

    /**
     * @test
     */
    public function assertKernelCanProvideSetUpEarlierParameters(): void
    {
        $this->assertTrue($this->container->hasParameter('std'));

        $param = $this->kernel->getKernelParameterService('std');

        $this->assertInstanceOf(\stdClass::class, $param);
        $this->assertEquals(getenv('APP_ENVIRONMENT'), $this->container->getParameter('ccet_environment'));
    }
}
