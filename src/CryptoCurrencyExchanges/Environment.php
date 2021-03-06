<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges;

use LogicException;

/**
 * Application environment.
 */
final class Environment
{
    private const ENVIRONMENT_PRODUCTION = 'prod';
    private const ENVIRONMENT_SANDBOX = 'dev';
    private const ENVIRONMENT_TESTING = 'test';
    private const ENVIRONMENT_BOOTING = 'boot';

    private const LIST = [
        self::ENVIRONMENT_PRODUCTION,
        self::ENVIRONMENT_SANDBOX,
        self::ENVIRONMENT_TESTING,
        self::ENVIRONMENT_BOOTING,
    ];

    /**
     * Application environment.
     *
     * @var string
     */
    private $environment;

    /**
     * @param string $environment
     */
    private function __construct(string $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Get a textual representation of the current environment.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->environment;
    }

    /**
     * Creating a test environment.
     *
     * @return self
     */
    public static function test(): self
    {
        return new self(self::ENVIRONMENT_TESTING);
    }

    /**
     * Creating a develop environment.
     *
     * @return self
     */
    public static function dev(): self
    {
        return new self(self::ENVIRONMENT_SANDBOX);
    }

    /**
     * Creating a production environment.
     *
     * @return self
     */
    public static function prod(): self
    {
        return new self(self::ENVIRONMENT_PRODUCTION);
    }

    /**
     * Creating a booting environment.
     *
     * @return self
     */
    public static function boot(): self
    {
        return new self(self::ENVIRONMENT_BOOTING);
    }

    /**
     * Creating the specified environment.
     *
     * @param string $environment
     * @param bool   $validate
     *
     * @return Environment
     */
    public static function create(string $environment, $validate = false): self
    {
        $environment = mb_strtolower($environment);

        if ($validate) {
            self::validateEnvironment($environment);
        }

        return new self($environment);
    }

    /**
     * Is this environment for debugging?
     *
     * @return bool
     */
    public function isDebug(): bool
    {
        return self::ENVIRONMENT_PRODUCTION !== $this->environment;
    }

    /**
     * Is this environment for testing?
     *
     * @return bool
     */
    public function isTesting(): bool
    {
        return self::ENVIRONMENT_TESTING === $this->environment;
    }

    /**
     * Is this environment for production usage?
     *
     * @return bool
     */
    public function isProduction(): bool
    {
        return self::ENVIRONMENT_PRODUCTION === $this->environment;
    }

    /**
     * Is this environment for development usage?
     *
     * @return bool
     */
    public function isDevelopment(): bool
    {
        return self::ENVIRONMENT_SANDBOX === $this->environment;
    }

    /**
     * Is environments equals.
     *
     * @param Environment $environment
     *
     * @return bool
     */
    public function equals(self $environment): bool
    {
        return $this->environment === $environment->environment;
    }

    /**
     * Validate the specified environment.
     *
     * @param string $specifiedEnvironment
     *
     * @throws LogicException The value of the environment is not specified, or is incorrect
     */
    private static function validateEnvironment(string $specifiedEnvironment): void
    {
        if ('' === $specifiedEnvironment ||
            false === \in_array($specifiedEnvironment, self::LIST, true)
        ) {
            throw new LogicException(
                sprintf(
                    'Provided incorrect value of the environment: "%s". Allowable values: %s',
                    $specifiedEnvironment, implode(', ', array_values(self::LIST))
                )
            );
        }
    }
}
