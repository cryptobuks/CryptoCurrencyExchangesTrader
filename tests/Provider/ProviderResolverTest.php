<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Tests\Provider;

use Kefzce\CryptoCurrencyExchanges\Provider\NullProvider;
use Kefzce\CryptoCurrencyExchanges\Provider\ProviderInterface;
use Kefzce\CryptoCurrencyExchanges\Provider\ProviderResolver;
use Kefzce\CryptoCurrencyExchanges\Tests\Provider\Stubs\FirstValidProvider;
use Kefzce\CryptoCurrencyExchanges\Tests\Provider\Stubs\SecondValidProvider;
use PHPUnit\Framework\TestCase;

/** @noinspection PhpUndefinedClassInspection */
final class ProviderResolverTest extends TestCase
{
    /** @var ProviderResolver */
    private $resolver;

    public function setUp()
    {
        $this->resolver = new ProviderResolver();
        parent::setUp();
    }

    /**
     * @dataProvider correctProviders
     *
     * @param ProviderInterface $provider
     */
    public function testCanAddFewProviders($provider): void
    {
        $this->assertEmpty($this->resolver->getProviders());
        $this->resolver->addProvider($provider);
        $this->assertNotEmpty($this->resolver->getProviders());
        $this->assertCount(1, $this->resolver->getProviders());
        $this->assertInstanceOf(ProviderInterface::class, $this->resolver->getProviders()[0]);
    }

    /**
     * @dataProvider incorrectProviders
     *
     * @param ProviderInterface[] ...$provider
     */
    public function testShouldThrowExceptionIfIncorrectTypeProvidersPassing($provider): void
    {
        $this->assertEmpty($this->resolver->getProviders());
        $this->expectException(\TypeError::class);
        $this->resolver->addProvider($provider);
        $this->assertNotEmpty($this->resolver->getProviders());
        $this->assertCount(0, $this->resolver->getProviders());
    }

    /**
     * @dataProvider duplicateProviders
     *
     * @param array $providers
     */
    public function testWillNotAllowAddDuplicatesProviderIntoStorageList(...$providers): void
    {
        $this->assertEmpty($this->resolver->getProviders());

        foreach ($providers as $provider) {
            $this->resolver->addProvider($provider);
        }

        $this->assertNotEmpty($this->resolver->getProviders());
        $this->assertCount(1, $this->resolver->getProviders());
        $this->assertInstanceOf(ProviderInterface::class, $this->resolver->getProviders()[0]);
    }

    /** @noinspection PhpUndefinedClassInspection */
    public function correctProviders(): ?\Generator
    {
        yield [new FirstValidProvider()];

        yield [new SecondValidProvider()];
    }

    /** @noinspection PhpUndefinedClassInspection */
    public function incorrectProviders(): ?\Generator
    {
        yield [new \stdClass()];

        yield [new \SplObjectStorage()];
    }

    /** @noinspection PhpUndefinedClassInspection */
    public function duplicateProviders(): ?\Generator
    {
        yield [new NullProvider()];

        yield [new NullProvider()];
    }
}
