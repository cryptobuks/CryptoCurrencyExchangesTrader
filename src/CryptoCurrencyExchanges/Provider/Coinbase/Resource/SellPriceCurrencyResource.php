<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource;

use Symfony\Component\Validator\Constraints as Assert;

final class SellPriceCurrencyResource
{
    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $base;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $currency;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $amount;
}
