<?php

declare(strict_types=1);

namespace App;

use App\DependencyInjection\Compiler\CommandPass;
use App\DependencyInjection\Compiler\ProviderPass;
use App\DependencyInjection\ContainerBuilder\ContainerBuilder;
use App\DependencyInjection\Extention\CryptoCurrencyExchangesExtention;
use Dotenv\Dotenv;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class Bootstrap
{
    private $containerBuilder;

    private function __construct()
    {
        $this->containerBuilder = new ContainerBuilder();

        $this->containerBuilder->addExtensions(new CryptoCurrencyExchangesExtention());
    }

    /**
     * @return ContainerInterface
     */
    public function boot(): ContainerInterface
    {
        $this->containerBuilder->addCompilerPasses(new ProviderPass());

        return $this->containerBuilder->build();
    }

    /**
     * @return Bootstrap
     */
    public static function withEnvironmentValues(): self
    {
        return new self();
    }

    /**
     * @param string $envFilePath
     *
     * @return Bootstrap
     */
    public static function withDotEnv(string $envFilePath): self
    {
        Dotenv::create($envFilePath);

        return new self();
    }

    /**
     * @return Bootstrap
     */
    public function registerConsoleCommands(): self
    {
        $this->containerBuilder->addCompilerPasses(new CommandPass());

        return $this;
    }
}
