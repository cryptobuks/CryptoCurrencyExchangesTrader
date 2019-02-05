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

    private function __construct($env = null)
    {
        $envValue = '' !== (string) getenv('APP_ENVIRONMENT')
            ? (string) getenv('APP_ENVIRONMENT')
            : 'dev';
        $this->containerBuilder = new ContainerBuilder(Environment::create($env ?? $envValue));
        $this->containerBuilder->addParameters(['ccet.environment' => $env ?? $envValue]);
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
        $t = Dotenv::create($envFilePath);

        $t->load();

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

    /**
     * @return Bootstrap
     */
    public function enableAutoImportsProviders(): self
    {
        $this->containerBuilder->addCompilerPasses(new ProviderPass());

        return $this;
    }

    /**
     * @param string $directory
     *
     * @return Bootstrap
     */
    public function useCustomCacheDirectory(string $directory): self
    {
        $this->containerBuilder->setCacheDirectoryPath($directory);

        return $this;
    }

    /**
     * @param array $params
     *
     * @return Bootstrap
     */
    public function addParameters(array  $params): self
    {
        $this->containerBuilder->addParameters($params);

        return $this;
    }

    /**
     * @param array $extensions
     *
     * @return Bootstrap
     */
    public function addExtension($extensions): self
    {
        $this->containerBuilder->addExtensions($extensions);

        return $this;
    }

    /**
     * @param string $parametersPath
     *
     * @return Bootstrap
     */
    public function provideParametersPath(string $parametersPath): self
    {
        if (file_exists($parametersPath) && is_readable($parametersPath)) {
            $parameters = include $parametersPath;
            $this->containerBuilder->addParameters($parameters);
        }

        return $this;
    }
}
