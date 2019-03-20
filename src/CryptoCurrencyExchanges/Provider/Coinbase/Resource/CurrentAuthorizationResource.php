<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource;

use Symfony\Component\Validator\Constraints as Assert;

final class CurrentAuthorizationResource
{
    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $method;

    /**
     * @Assert\NotBlank()
     *
     * @var array
     */
    public $scopes;
}
