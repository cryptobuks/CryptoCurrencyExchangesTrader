<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider\Coinbase;

use Kefzce\CryptoCurrencyExchanges\Mapper\JsonMapper;
use Kefzce\CryptoCurrencyExchanges\Mapper\UnableToFindResourceException;
use Kefzce\CryptoCurrencyExchanges\Provider\BaseProvider;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\CurrenciesResource;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\CurrentUserResource;
use Kefzce\CryptoCurrencyExchanges\Provider\ProviderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

final class CoinbaseProvider extends BaseProvider implements ProviderInterface
{
    public const API_ENDPOINT = 'https://api.coinbase.com';
    public const PROVIDER_URI = 'https://www.coinbase.com';
    public const PROVIDER_DOC = 'https://developers.coinbase.com/api/v2';
    public const PROVIDER_FEES = 'https://support.coinbase.com/customer/portal/articles/2109597-buy-sell-bank-transfer-fees';

    /**
     * @var \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\HttpClient
     */
    private $client;

    /**
     * @var \Kefzce\CryptoCurrencyExchanges\Mapper\JsonMapper
     */
    private $mapper;

    public function __construct(HttpClient $client, JsonMapper $mapper)
    {
        $this->client = $client;
        $this->mapper = $mapper;
    }

    /**
     * @param array  $params
     * @param string $classMap
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array|mixed|object|void
     */
    public function getCurrencies(array $params = [], $classMap = CurrenciesResource::class)
    {
        return $this->getAndMapData('/v2/currencies', $params, $classMap);
    }

    /**
     * @param array  $params
     * @param string $classMap
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return array|mixed|object|void
     */
    public function getCurrentUser(array $params = [], $classMap = CurrentUserResource::class)
    {
        return $this->getAndMapData('/v2/user', $params, $classMap, );
    }

    /**
     * @param string $path
     * @param array  $params
     * @param string $classMap
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Kefzce\CryptoCurrencyExchanges\Mapper\UnableToFindResourceException
     *
     * @return array|mixed|object|void
     */
    private function getAndMapData(string $path, array $params, string $classMap)
    {
        $request = $this->client->request('GET', $path, $params);

        $response = $request->getBody()->getContents();

        $bag = new ParameterBag(
            json_decode($response, true)['data']
        );

        if (!class_exists($classMap)) {
            throw  new UnableToFindResourceException(sprintf(
                'Unable to find provided resource "%s"',
                $classMap
            ));
        }

        $object = $this->mapper->convert(
            $bag->all(),
            $classMap
        );

        return $object;
    }
}
