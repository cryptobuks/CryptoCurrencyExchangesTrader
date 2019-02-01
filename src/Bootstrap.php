<?php

declare(strict_types=1);

namespace App;

use App\DependencyInjection\Compiler\CommandPass;
use App\DependencyInjection\Compiler\ProviderPass;
use App\DependencyInjection\ContainerBuilder\ContainerBuilder;
use App\DependencyInjection\Extension\CryptoCurrencyExchangesExtension;
use Dotenv\Dotenv;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class Bootstrap
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    private function __construct()
    {
        $this->containerBuilder = new ContainerBuilder();

        $this->containerBuilder->addExtensions(new CryptoCurrencyExchangesExtension());
    }

    /**
     * @return ContainerInterface
     */
    public function boot(): ContainerInterface
    {
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

    public function enableAutoImportsProviders(): self
    {
        $this->containerBuilder->addCompilerPasses(new ProviderPass());
    }

    public function useCustomCacheDirectory(string $directory): self
    {
        $this->containerBuilder->setCacheDirectoryPath($directory);

        return $this;
    }
}
