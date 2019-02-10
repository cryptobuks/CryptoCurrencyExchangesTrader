<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider\Coinbase;

use Kefzce\CryptoCurrencyExchanges\Provider\BaseProvider;
use Kefzce\CryptoCurrencyExchanges\Provider\ProviderInterface;

final class CoinbaseProvider extends BaseProvider implements ProviderInterface
{
    public const API_ENDPOINT = 'https://api.coinbase.com';
    public const PROVIDER_URI = 'https://www.coinbase.com';
    public const PROVIDER_DOC = 'https://developers.coinbase.com/api/v2';
    public const PROVIDER_FEES = 'https://support.coinbase.com/customer/portal/articles/2109597-buy-sell-bank-transfer-fees';

    /**
     * @var \Kefzce\CryptoCurrencyExchanges\Provider\HttpClient
     */
    private $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param array $params
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return string
     */
    public function getCurrencies(array $params = []): string
    {
        return $this->getAndMapData('/v2/currencies', $params);
    }

    /**
     * @param array $params
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return string
     */
    public function getCurrentUser(array $params = []): string
    {
        return $this->getAndMapData('/v2/user', $params);
    }

    /**
     * @param $path
     * @param array $params
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return string
     */
    private function getAndMapData($path, array $params): string
    {
        $response = $this->client->request('GET', $path, $params);

        return $response->getBody()->getContents();
    }
}
