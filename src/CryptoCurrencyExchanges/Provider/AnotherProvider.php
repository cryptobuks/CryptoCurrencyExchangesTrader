<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider;

use GuzzleHttp\ClientInterface;
use Kefzce\CryptoCurrencyExchanges\Http\ClientBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AnotherProvider implements ProviderInterface, Configurable
{
    public const ENDPOINT = 'http://test.api';

    /**
     * @var array
     */
    private $config;

    /** @var ClientInterface */
    private $client;

    /**
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client = null)
    {
        $this->client = $client ?? ClientBuilder::build();
    }

    public function __toString(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    /**
     * @return string
     */
    public function describe(): string
    {
        return sprintf(
            'Your provider information FQCN: "%s", service tag: "%s"',
            \get_class($this),
            ProviderInterface::SERVICE_TAG,
        );
    }

    /**
     * @param array|null $config
     */
    public function provide(array $config = []): void
    {
        $resolver = new OptionsResolver();

        $resolver
            ->setRequired(['provider', 'test'])
            ->setAllowedTypes('provider', ['string'])
            ->setAllowedTypes('test', ['int'])
            ->setAllowedValues('test', function ($value) {
                return \is_int($value);
            })
            ->setAllowedValues('provider', function ($value) {
                return \is_string($value);
            });

        $this->config = $resolver->resolve($config);
    }
}
