<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Tests\Functional\CoinbaseProvider;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Kefzce\CryptoCurrencyExchanges\Converter\ObjectConverter;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\CoinbaseProvider;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\HttpClient;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\Country;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\CurrentUserResource;
use Kefzce\CryptoCurrencyExchanges\Serializer\SerializerFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class CoinbaseProviderTest extends TestCase
{
    /** @var MockHandler */
    private $mockHandler;

    /** @var CoinbaseProvider */
    private $provider;

    public function setUp()
    {
        parent::setUp();

        $this->mockHandler = new MockHandler();

        $handlerStack = HandlerStack::create($this->mockHandler);

        $transport = new Client([
            'handler' => $handlerStack,
        ]);

        $this->provider = new CoinbaseProvider(
            new HttpClient(
                $transport
            ), new ObjectConverter(
                SerializerFactory::createSerializer(),
                Validation::createValidator()
            )
        );
    }

    /**
     * @covers \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\CoinbaseProvider::getCurrentUser
     * @covers \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\CoinbaseProvider::getAndMapData
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Safe\Exceptions\JsonException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testProviderCanReturnAndDecodeCurrentUserResponse(): void
    {
        $this->appendCurrentUserResponse();

        /** @var CurrentUserResource $response */
        $response = $this->provider->getCurrentUser();

        $this->assertNotEmpty($response);

        $this->assertInstanceOf(CurrentUserResource::class, $response);
        $this->assertSame('f0ec8736-3ec8-434f-8fb4-74dc15d10eda', $response->id);
        $this->assertSame('test', $response->name);
        $this->assertSame('testing', $response->username);
        $this->assertNull($response->profileLocation);
        $this->assertNull($response->profileBio);
        $this->assertNull($response->profileUrl);
        $this->assertSame(
            'https://images.coinbase.com/avatar?h=5c5fcb6c5fad9f059b2b23SDkWrx6zQMRmMmlqsFmExaFYlt5kKc1wSfKO2C%0AQCpO&s=128',
            $response->avatarUrl
        );
        $this->assertSame('user', $response->resource);
        $this->assertSame('/v2/user', $response->resourcePath);
        $this->assertSame('testing@gmail.com', $response->email);
        $this->assertSame('Pacific Time (US & Canada)', $response->timeZone);
        $this->assertSame('UAH', $response->nativeCurrency);
        $this->assertSame('BTC', $response->bitcoinUnit);
        $this->assertInstanceOf(Country::class, $response->country);
        $this->assertNotEmpty($response->country);
        $this->assertSame('UA', $response->country->code);
        $this->assertSame('Ukraine', $response->country->name);
        $this->assertFalse($response->country->isInEurope);
        $this->assertNull($response->state);
        $this->assertFalse($response->regionSupportsFiatTransfers);
        $this->assertFalse($response->regionSupportsCryptoToCryptoTransfers);
        $this->assertSame('2019-02-10T06:57:48Z', $response->createdAt);
        $this->assertNull($response->tiers);
    }

    private function appendCurrentUserResponse(): void
    {
        $this->mockHandler->append(
            new Response(200, [], <<<JSON
            {"data":{"id":"f0ec8736-3ec8-434f-8fb4-74dc15d10eda","name":"test","username":"testing","profile_location":null,"profile_bio":null,"profile_url":null,"avatar_url":"https://images.coinbase.com/avatar?h=5c5fcb6c5fad9f059b2b23SDkWrx6zQMRmMmlqsFmExaFYlt5kKc1wSfKO2C%0AQCpO\u0026s=128","resource":"user","resource_path":"/v2/user","email":"testing@gmail.com","time_zone":"Pacific Time (US \u0026 Canada)","native_currency":"UAH","bitcoin_unit":"BTC","state":null,"country":{"code":"UA","name":"Ukraine","is_in_europe":false},"region_supports_fiat_transfers":false,"region_supports_crypto_to_crypto_transfers":false,"created_at":"2019-02-10T06:57:48Z",
"referral_money":{"amount":"268.65","currency":"UAH","currency_symbol":"₴","referral_threshold":"2686.50"}}}
JSON
            )
        );
    }
}
