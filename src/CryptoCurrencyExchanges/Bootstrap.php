<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges;

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Dotenv\Dotenv;
use Kefzce\CryptoCurrencyExchanges\DependencyInjection\Compiler\CommandPass;
use Kefzce\CryptoCurrencyExchanges\DependencyInjection\Compiler\ProviderPass;
use Kefzce\CryptoCurrencyExchanges\DependencyInjection\ContainerBuilder\ContainerBuilder;
use Kefzce\CryptoCurrencyExchanges\DependencyInjection\Extension\KefzceCryptoCurrencyExchangesExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class Bootstrap
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @param string|null $env
     */
    private function __construct($env = null)
    {
        $envValue = '' !== (string) getenv('APP_ENVIRONMENT')
            ? (string) getenv('APP_ENVIRONMENT')
            : 'dev';
        $this->containerBuilder = new ContainerBuilder(Environment::create($env ?? $envValue));
        $this->containerBuilder->addParameters(['ccet_environment' => $env ?? $envValue]);
        $this->containerBuilder->addExtensions(new KefzceCryptoCurrencyExchangesExtension());
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
     * @param Extension[] $extensions
     *
     * @return Bootstrap
     */
    public function addExtension(Extension ...$extensions): self
    {
        $this->containerBuilder->addExtensions(...$extensions);

        return $this;
    }

    /**
     * @param string $parametersPath
     * @psalm-suppress MixedAssignment
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

    /**
     * @param ClassLoader $loader
     * @psalm-suppress DeprecatedMethod This method is deprecated and will be removed in doctrine/annotations 2.0
     *
     * @return $this
     */
    public function registerLoader(ClassLoader $loader): self
    {
        /* @noinspection   PhpDeprecationInspection */
        AnnotationRegistry::registerLoader([$loader, 'loadClass']);

        return $this;
    }
}
