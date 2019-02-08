<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Http;

use GuzzleHttp\Client;

final class ClientBuilder implements HttpBuilderInterface
{
    /**
     * @todo Provide additional lists USERAGENT for merging
     */
    public const USERAGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36';

    public static function build(array $options = []): Client
    {
        return new Client(
            array_merge(['headers' => ['User-Agent' => self::USERAGENT]], $options)
        );
    }
}
