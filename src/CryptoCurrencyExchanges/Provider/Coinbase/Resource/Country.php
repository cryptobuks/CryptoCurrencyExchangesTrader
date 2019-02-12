<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource;

use Symfony\Component\Validator\Constraints as Assert;

final class Country
{
    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $code;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $name;

    /**
     * @Assert\NotBlank()
     *
     * @var bool
     */
    public $isInEurope;
}
