<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider\Coinbase;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Kefzce\CryptoCurrencyExchanges\Http\ClientBuilder;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Enum\Coinbase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClient
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $transport;

    /**
     * @var string
     */
    private $caBundle;

    /**
     * @param \GuzzleHttp\ClientInterface $transport
     */
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
     * @param string $method
     * @param string $path
     * @param array  $params
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function request(string $method, string $path, array $params = []): ResponseInterface
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
            throw RequestException::wrapException($request, $e);
        }

        return $response;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array  $params
     * @psalm-suppress MixedAssignment
     *
     * @return array
     */
    private function prepareOptions(string $method, string $path, array $params = []): array
    {
        $options = [];

        if ($this->caBundle) {
            $options[RequestOptions::VERIFY] = $this->caBundle;
        }
        // omit two_factor_token
        $data = array_diff_key($params, [Coinbase::TWO_FACTOR_TOKEN => true]);

        if (!empty($data)) {
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
     * @param string $method
     * @param string $path
     * @param string $body
     * @psalm-suppress TypeDoesNotContainType
     *
     * @return array
     */
    private function getRequestHeaders(string $method, string $path, string $body): array
    {
        $timestamp = time();
        $apiKey = getenv('COINBASE_API_KEY') ?: '';
        $apiSecret = getenv('COINBASE_API_SECRET') ?: '';

        if (false === $apiKey && false === $apiSecret) {
            throw new \RuntimeException('One or more environment variables missing, make sure you provide "COINBASE_API_KEY", "COINBASE_API_SECRET"');
        }
        $signature = hash_hmac(
            'sha256',
            $timestamp . $method . $path . $body,
            $apiSecret
        );

        return [
            'CB-ACCESS-KEY' => $apiKey,
            'CB-ACCESS-SIGN' => $signature,
            'CB-ACCESS-TIMESTAMP' => $timestamp,
        ];
    }
}
