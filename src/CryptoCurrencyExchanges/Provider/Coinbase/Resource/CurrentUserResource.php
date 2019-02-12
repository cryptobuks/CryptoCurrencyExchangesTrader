<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource;

use Symfony\Component\Validator\Constraints as Assert;

final class CurrentUserResource
{
    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $id;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $name;

    /**
     * @Assert\Blank()
     *
     * @var string|null
     */
    public $username;

    /**
     * @Assert\Blank()
     *
     * @var string|null
     */
    public $profileLocation;

    /**
     * @Assert\Blank()
     *
     * @var string|null
     */
    public $profileBio;

    /**
     * @Assert\Blank()
     *
     * @var string|null
     */
    public $profileUrl;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $avatarUrl;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $resource;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $resourcePath;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $email;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $timeZone;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $nativeCurrency;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $bitcoinUnit;

    /**
     * @var \Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource\Country
     */
    public $country;

    /**
     * @Assert\Blank()
     *
     * @var string|null
     */
    public $state;

    /**
     * @var bool
     */
    public $regionSupportsFiatTransfers;

    /**
     * @var bool
     */
    public $regionSupportsCryptoToCryptoTransfers;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    public $createdAt;

    /**
     * @var Tiers
     */
    public $tiers;
}
