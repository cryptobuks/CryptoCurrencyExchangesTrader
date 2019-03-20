<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider\Coinbase;

use Kefzce\CryptoCurrencyExchanges\Converter\ObjectConverter;
use Kefzce\CryptoCurrencyExchanges\Provider\BaseProvider;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\CurrenciesResource;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\CurrentUserResource;
use Kefzce\CryptoCurrencyExchanges\Provider\ProviderInterface;
use Psr\Http\Message\ResponseInterface;
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
     * @var \Kefzce\CryptoCurrencyExchanges\Converter\ObjectConverter
     */
    private $converter;

    /**
     * @param \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\HttpClient $client
     * @param \Kefzce\CryptoCurrencyExchanges\Converter\ObjectConverter    $objectConverter
     */
    public function __construct(HttpClient $client, ObjectConverter $objectConverter)
    {
        $this->client = $client;
        $this->converter = $objectConverter;
    }

    /**
     * @param array  $params
     * @param string $classMap
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Safe\Exceptions\JsonException
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
     * @throws \Safe\Exceptions\JsonException
     *
     * @return array|mixed|object|void
     */
    public function getCurrentUser(array $params = [], $classMap = CurrentUserResource::class)
    {
        return $this->getAndMapData('/v2/user', $params, $classMap);
    }

    /**
     * @param string $path
     * @param array  $params
     * @param string $classMap
     *
     * @psalm-suppress MixedAssignment
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Kefzce\CryptoCurrencyExchanges\Converter\UnableToFindResourceException
     * @throws \Safe\Exceptions\JsonException
     *
     * @return array|mixed|object
     */
    private function getAndMapData(string $path, array $params, string $classMap)
    {
        $request = $this->client->request('GET', $path, $params);

        $bag = $this->decodeAndReturnBag($request);

        $object = $this->converter->convert(
            $bag->all(),
            $classMap
        );

        return $object;
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $request
     *
     * @throws \Safe\Exceptions\JsonException
     *
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArgument
     * @psalm-suppress MixedArrayAccess
     *
     * @return \Symfony\Component\HttpFoundation\ParameterBag
     */
    private function decodeAndReturnBag(ResponseInterface $request): ParameterBag
    {
        $response = $request->getBody()->getContents();
        $rawData = \Safe\json_decode($response, true);

        return new ParameterBag($rawData['data']);
    }
}
