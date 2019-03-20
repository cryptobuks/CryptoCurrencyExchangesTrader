<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource;

use Symfony\Component\Validator\Constraints as Assert;

final class CurrentServiceTimeResource
{
    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $iso;

    /**
     * @Assert\NotBlank()
     *
     * @var int
     */
    public $epoch;
}
