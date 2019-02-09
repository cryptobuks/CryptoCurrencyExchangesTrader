<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Kefzce\CryptoCurrencyExchanges\Http\ClientBuilder;
use Kefzce\CryptoCurrencyExchanges\Provider\Enum\Coinbase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClient
{
    /**
     * @var \GuzzleHttp\ClientInterface |\GuzzleHttp\Client
     */
    private $transport;

    /**
     * @var string
     */
    private $caBundle;

    public function __construct(ClientInterface $transport)
    {
        $this->transport = $transport;
    }

    /**
     * @param string $caBundle
     */
    public function setCaBundle(string $caBundle): void
    {
        $this->caBundle = $caBundle;
    }

    /**
     * @param $method
     * @param $path
     * @param array $params
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function request($method, $path, array $params = []): ResponseInterface
    {
        $request = new Request(
            $method,
            CoinbaseProvider::API_ENDPOINT . $path
        );

        return $this->send($request, $params);
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param array                              $params
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function send(RequestInterface $request, array $params = []): ResponseInterface
    {
        $options = $this->prepareOptions(
            $request->getMethod(),
            $request->getRequestTarget(),
            $params
        );

        try {
            $response = $this->transport->send($request, $options);
        } catch (RequestException $e) {
            throw RequestException::wrapException($e);
        }

        return $response;
    }

    /**
     * @param $method
     * @param $path
     * @param array $params
     *
     * @return array
     */
    private function prepareOptions($method, $path, array $params = []): array
    {
        $options = [];

        if ($this->caBundle) {
            $options[RequestOptions::VERIFY] = $this->caBundle;
        }
        // omit two_factor_token
        $data = array_diff_key($params, [Coinbase::TWO_FACTOR_TOKEN => true]);

        if ($data) {
            $options[RequestOptions::JSON] = $data;
            $body = json_encode($data);
        } else {
            $body = '';
        }

        $defaultHeaders = [
            'User-Agent' => ClientBuilder::USERAGENT,
            'CB-VERSION' => '/v2',
            'Content-Type' => 'application/json',
        ];

        if (isset($params[Coinbase::TWO_FACTOR_TOKEN])) {
            $defaultHeaders['CB-2FA-TOKEN'] = $params[Coinbase::TWO_FACTOR_TOKEN];
        }

        $options[RequestOptions::HEADERS] = $defaultHeaders + $this->getRequestHeaders(
                $method,
                $path,
                $body
            );

        return $options;
    }

    /**
     * @param $method
     * @param $path
     * @param $body
     *
     * @return array
     */
    private function getRequestHeaders($method, $path, $body): array
    {
        $timestamp = time();
        $signature = hash_hmac(
            'sha256',
            $timestamp . $method . $path . $body,
            getenv('COINBASE_API_SECRET')
        );

        return [
            'CB-ACCESS-KEY' => getenv('COINBASE_API_KEY'),
            'CB-ACCESS-SIGN' => $signature,
            'CB-ACCESS-TIMESTAMP' => $timestamp,
        ];
    }
}
