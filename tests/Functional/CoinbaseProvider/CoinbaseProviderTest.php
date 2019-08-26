<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Tests\Functional\CoinbaseProvider;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Kefzce\CryptoCurrencyExchanges\Converter\ObjectConverter;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\CoinbaseProvider;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\HttpClient;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\BuyPriceCurrencyResource;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\Country;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\CurrentAuthorizationResource;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\CurrentServiceTimeResource;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\CurrentUserResource;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\ExchangeRatesResource;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\SellPriceCurrencyResource;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\SpotPriceCurrencyResource;
use Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\UserResource;
use Kefzce\CryptoCurrencyExchanges\Serializer\SerializerFactory;
use PHPUnit\Framework\TestCase;
use Safe\Exceptions\JsonException;
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
     * @throws GuzzleException
     * @throws JsonException
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

    /**
     * @covers \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\CoinbaseProvider::getExchangeRates
     * @covers \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\CoinbaseProvider::getAndMapData
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testProviderCanReturnAndDecodeExchangeRates(): void
    {
        $this->appendExchangeRatesResponse();

        /** @var ExchangeRatesResource $response */
        $response = $this->provider->getExchangeRates();

        $this->assertNotEmpty($response);
        $this->assertSame('USD', $response->currency);
        $this->assertIsArray($response->rates);
        $this->assertNotEmpty($response->rates);
        $this->assertArrayHasKey('USD', $response->rates);

        $this->assertInstanceOf(ExchangeRatesResource::class, $response);
    }

    /**
     * @covers \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\CoinbaseProvider::getBuyPrice
     * @covers \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\CoinbaseProvider::getAndMapData
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testProviderCanReturnBuyPriceCurrency(): void
    {
        $this->appendBuyPriceCurrency();

        /** @var BuyPriceCurrencyResource $response */
        $response = $this->provider->getBuyPrice('USD');

        $this->assertNotEmpty($response);
        $this->assertSame('BTC', $response->base);
        $this->assertSame('USD', $response->currency);
        $this->assertSame('4018.63', $response->amount);

        $this->assertInstanceOf(BuyPriceCurrencyResource::class, $response);
    }

    /**
     * @covers \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\CoinbaseProvider::getSellPrice
     * @covers \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\CoinbaseProvider::getAndMapData
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testProviderCanReturnSellPriceCurrency(): void
    {
        $this->appendSellPriceCurrency();

        /** @var SellPriceCurrencyResource $response */
        $response = $this->provider->getSellPrice('USD');

        $this->assertNotEmpty($response);

        $this->assertSame('BTC', $response->base);
        $this->assertSame('USD', $response->currency);
        $this->assertSame('3978.08', $response->amount);

        $this->assertInstanceOf(SellPriceCurrencyResource::class, $response);
    }

    /**
     * @covers \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\CoinbaseProvider::getSpotPrice
     * @covers \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\CoinbaseProvider::getAndMapData
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testProviderCanReturnSpotPriceCurrency(): void
    {
        $this->appendSpotPriceCurrency();

        /** @var SellPriceCurrencyResource $response */
        $response = $this->provider->getSpotPrice('USD');

        $this->assertNotEmpty($response);

        $this->assertSame('BTC', $response->base);
        $this->assertSame('USD', $response->currency);
        $this->assertSame('3998.46', $response->amount);

        $this->assertInstanceOf(SpotPriceCurrencyResource::class, $response);
    }

    /**
     * @covers \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\CoinbaseProvider::getCurrentServiceTime
     * @covers \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\CoinbaseProvider::getAndMapData
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testProviderCanReturnCurrentServiceTime(): void
    {
        $this->appendCurrentServiceTime();

        /** @var CurrentServiceTimeResource $response */
        $response = $this->provider->getCurrentServiceTime();

        $this->assertNotEmpty($response);

        $this->assertSame('2019-03-20T13:58:00Z', $response->iso);
        $this->assertSame(1553090280, $response->epoch);

        $this->assertInstanceOf(CurrentServiceTimeResource::class, $response);
    }

    /**
     * @covers \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\CoinbaseProvider::getCurrentAuthorization
     * @covers \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\CoinbaseProvider::getAndMapData
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testProviderCanReturnCurrentAuthorization(): void
    {
        $this->appendCurrentAuthorization();

        /** @var CurrentAuthorizationResource $response */
        $response = $this->provider->getCurrentAuthorization();

        $this->assertNotEmpty($response);

        $this->assertSame('api_key', $response->method);
        $this->assertIsArray($response->scopes);

        $this->assertInstanceOf(CurrentAuthorizationResource::class, $response);
    }

    /**
     * @covers \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\CoinbaseProvider::getUser
     * @covers \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\CoinbaseProvider::getAndMapData
     *
     * @throws GuzzleException
     * @throws JsonException
     */
    public function testProviderCanReturnUser(): void
    {
        $this->appendUser();

        /** @var UserResource $response */
        $response = $this->provider->getUser('f0ec8736-3ec8-434f-8fb4-74dc15d10eda');

        $this->assertNotEmpty($response);

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

        $this->assertInstanceOf(UserResource::class, $response);
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

    private function appendExchangeRatesResponse(): void
    {
        $this->mockHandler->append(
          new Response(200, [], <<<JSON
{"data":{"currency":"USD","rates":{"AED":"3.67","AFN":"75.84","ALL":"110.18","AMD":"485.99","ANG":"1.83","AOA":"315.61","ARS":"40.55","AUD":"1.41","AWG":"1.80","AZN":"1.70","BAM":"1.72","BAT":"5.21971067","BBD":"2.00","BCH":"0.00632031","BDT":"84.34","BGN":"1.72","BHD":"0.377","BIF":"1805","BMD":"1.00","BND":"1.35","BOB":"6.91","BRL":"3.79","BSD":"1.00","BSV":"0.01516299","BTC":"0.00025000","BTN":"68.77","BWP":"10.70","BYN":"2.10","BYR":"21040","BZD":"2.01","CAD":"1.33","CDF":"1646.30","CHF":"1.00","CLF":"0.0242","CLP":"665","CNH":"6.70","CNY":"6.70","COP":"3097.41","CRC":"596.32","CUC":"1.00","CVE":"97.20","CZK":"22.58","DJF":"178","DKK":"6.57","DOP":"50.79","DZD":"118.67","EEK":"14.61","EGP":"17.26","ERN":"15.00","ETB":"28.46","ETC":"0.22050717","ETH":"0.00725295","EUR":"0.88","FJD":"2.13","FKP":"0.76","GBP":"0.76","GEL":"2.68","GGP":"0.76","GHS":"5.47","GIP":"0.76","GMD":"49.60","GNF":"9121","GTQ":"7.69","GYD":"208.69","HKD":"7.85","HNL":"24.43","HRK":"6.53","HTG":"83.23","HUF":"276","IDR":"14171.00","ILS":"3.61","IMP":"0.76","INR":"68.81","IQD":"1192.718","ISK":"117","JEP":"0.76","JMD":"124.57","JOD":"0.709","JPY":"111","KES":"101.06","KGS":"68.68","KHR":"4022.87","KMF":"434","KRW":"1129","KWD":"0.304","KYD":"0.83","KZT":"378.22","LAK":"8583.37","LBP":"1511.28","LKR":"178.42","LRD":"162.17","LSL":"14.44","LTC":"0.01675322","LTL":"3.22","LVL":"0.66","LYD":"1.385","MAD":"9.59","MDL":"17.24","MGA":"3553.4","MKD":"54.18","MMK":"1540.87","MNT":"2513.25","MOP":"8.08","MRO":"357.0","MTL":"0.68","MUR":"34.63","MVR":"15.50","MWK":"724.96","MXN":"18.93","MYR":"4.06","MZN":"62.78","NAD":"14.44","NGN":"360.10","NIO":"32.87","NOK":"8.53","NPR":"110.03","NZD":"1.46","OMR":"0.385","PAB":"1.00","PEN":"3.30","PGK":"3.37","PHP":"52.86","PKR":"139.62","PLN":"3.77","PYG":"6132","QAR":"3.64","RON":"4.19","RSD":"103.74","RUB":"64.31","RWF":"901","SAR":"3.75","SBD":"8.15","SCR":"13.66","SEK":"9.18","SGD":"1.35","SHP":"0.76","SLL":"8390.00","SOS":"578.28","SRD":"7.46","SSP":"130.26","STD":"21050.60","SVC":"8.75","SZL":"14.44","THB":"31.73","TJS":"9.44","TMT":"3.50","TND":"3.012","TOP":"2.26","TRY":"5.47","TTD":"6.78","TWD":"30.81","TZS":"2345.00","UAH":"27.15","UGX":"3706","USD":"1.00","USDC":"1.000000","UYU":"33.22","UZS":"8371.91","VEF":"248487.64","VES":"3291.52","VND":"23206","VUV":"111","WST":"2.60","XAF":"577","XAG":"0","XAU":"0","XCD":"2.70","XDR":"1","XLM":"9.1156456","XOF":"577","XPD":"0","XPF":"105","XPT":"0","XRP":"3.201537","YER":"250.40","ZAR":"14.42","ZEC":"0.01704594","ZMK":"5253.08","ZMW":"12.01","ZRX":"3.71177337","ZWL":"322.36"}}}
JSON
)
        );
    }

    private function appendBuyPriceCurrency(): void
    {
        $this->mockHandler->append(
            new Response(200, [], <<<JSON
{"data":{"base":"BTC","currency":"USD","amount":"4018.63"}}
JSON
)
        );
    }

    private function appendSellPriceCurrency(): void
    {
        $this->mockHandler->append(
            new Response(200, [], <<<JSON
{"data":{"base":"BTC","currency":"USD","amount":"3978.08"}}
JSON
)
        );
    }

    private function appendSpotPriceCurrency(): void
    {
        $this->mockHandler->append(
            new Response(200, [], <<<JSON
{"data":{"base":"BTC","currency":"USD","amount":"3998.46"}}
JSON
)
        );
    }

    private function appendCurrentServiceTime(): void
    {
        $this->mockHandler->append(
            new Response(200, [], <<<JSON
{"data":{"iso":"2019-03-20T13:58:00Z","epoch":1553090280}}
JSON
)
        );
    }

    private function appendCurrentAuthorization(): void
    {
        $this->mockHandler->append(
            new Response(200, [], <<<JSON
{"data":{"method":"api_key","scopes":["wallet:accounts:create","wallet:accounts:delete","wallet:accounts:read","wallet:accounts:update","wallet:addresses:create","wallet:addresses:read","wallet:buys:create","wallet:buys:read","wallet:checkouts:create","wallet:checkouts:read","wallet:contacts:read","wallet:deposits:create","wallet:deposits:read","wallet:notifications:read","wallet:orders:create","wallet:orders:read","wallet:orders:refund","wallet:payment-methods:delete","wallet:payment-methods:limits","wallet:payment-methods:read","wallet:sells:create","wallet:sells:read","wallet:supported-assets:read","wallet:trades:create","wallet:trades:read","wallet:transactions:read","wallet:transactions:request","wallet:transactions:send","wallet:transactions:transfer","wallet:user:email","wallet:user:read","wallet:user:update","wallet:withdrawals:create","wallet:withdrawals:read"]}}
JSON
)
        );
    }

    private function appendUser(): void
    {
        $this->mockHandler->append(
            new Response(200, [], <<<JSON
{"data":{"id":"f0ec8736-3ec8-434f-8fb4-74dc15d10eda","name":"test","username":"testing","profile_location":null,"profile_bio":null,"profile_url":null,"avatar_url":"https://images.coinbase.com/avatar?h=5c5fcb6c5fad9f059b2b23SDkWrx6zQMRmMmlqsFmExaFYlt5kKc1wSfKO2C%0AQCpO\u0026s=128","resource":"user","resource_path":"/v2/user","email":"testing@gmail.com"}}
JSON
)
        );
    }
}
