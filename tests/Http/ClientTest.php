<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Tests\Http;

use GuzzleHttp\ClientInterface;
use Kefzce\CryptoCurrencyExchanges\Http\ClientBuilder;
use PHPUnit\Framework\TestCase;
use stdClass;

class ClientTest extends TestCase
{
    /**
     * @dataProvider OptionsProvider
     *
     * @param array $options
     */
    public function testCanCreateClient(array $options): void
    {
        $client = ClientBuilder::build($options);
        $this->assertInstanceOf(ClientInterface::class, $client);
        $this->assertEquals(
            $client->getConfig(array_key_first($options)),
            $options[array_key_first($options)]
        );

        $this->assertSame(
            ClientBuilder::USERAGENT,
            $client->getConfig('headers')['User-Agent'],
        );
    }

    /**
     * @return array
     */
    public function OptionsProvider(): array
    {
        return [
            [['option' => 'value']],
            [['anotherOption' => 'newValue']],
            [['integer' => 123]],
            [['class' => new stdClass()]],
        ];
    }

    /**
     * @return array
     */
    public function IncorrectOptionProvider(): array
    {
        return [
            ['option' => 'value'],
            ['anotherOption' => 'newValue'],
            ['integer' => 123],
            ['class' => new stdClass()],
        ];
    }

    /**
     * @dataProvider IncorrectOptionProvider
     * @expectedException \TypeError
     *
     * @param $options
     */
    public function testShouldThrowExceptionIfIncorrectOptionsSet($options): void
    {
        $client = ClientBuilder::build($options);
    }
}
