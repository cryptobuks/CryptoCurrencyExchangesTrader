<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Resource;

use Symfony\Component\Validator\Constraints as Assert;

final class UserResource
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
}
