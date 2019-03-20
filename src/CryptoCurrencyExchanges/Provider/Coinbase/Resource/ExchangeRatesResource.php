<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource;

use Symfony\Component\Validator\Constraints as Assert;

final class ExchangeRatesResource
{
    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $currency;

    /**
     * @var array
     */
    public $rates;
}
