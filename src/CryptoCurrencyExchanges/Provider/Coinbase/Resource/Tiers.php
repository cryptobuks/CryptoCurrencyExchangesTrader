<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource;

use Symfony\Component\Validator\Constraints as Assert;

final class Tiers
{
    /**
     * @Assert\Blank()
     *
     * @var null
     */
    public $completedDescription;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $upgradeButtonText;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $header;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $body;
}
