<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\DependencyInjection\ContainerBuilder;

use Kefzce\CryptoCurrencyExchanges\Environment;
use SplObjectStorage;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

final class ContainerBuilder
{
    private const CONTAINER_NAME_TEMPLATE = '%s%sProjectContainer';

    /**
     * @var SplObjectStorage
     */
    private $compilerPasses;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var SplObjectStorage
     */
    private $extensions;

    /**
     * @var string
     */
    private $cacheDirectory = __DIR__ . '/../../../../var/cache';

    /**
     * @var ConfigCache|null
     */
    private $configCache;

    public function __construct(Environment $environment)
    {
        $compilerPassCollection = new SplObjectStorage();
        $extensionsCollection = new SplObjectStorage();
        $this->compilerPasses = $compilerPassCollection;
        $this->extensions = $extensionsCollection;
        $this->parameters = [];
        $this->environment = $environment;
    }

    /**
     * @param Extension ...$extensions
     */
    public function addExtensions(Extension ...$extensions): void
    {
        foreach ($extensions as $extension) {
            $this->extensions->attach($extension);
        }
    }

    /**
     * @param CompilerPassInterface ...$compilerPasses
     */
    public function addCompilerPasses(CompilerPassInterface ...$compilerPasses): void
    {
        foreach ($compilerPasses as $compilerPass) {
            $this->compilerPasses->attach($compilerPass);
        }
    }

    /**
     * @psalm-suppress MixedAssignment
     *
     * @param array $parameters
     */
    public function addParameters(array $parameters): void
    {
        foreach ($parameters as $key => $value) {
            $this->parameters[$key] = $value;
        }
    }

    /**
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedTypeCoercion
     *
     * @return ContainerInterface
     */
    public function build(): ContainerInterface
    {
        $containerBuilder = new SymfonyContainerBuilder(new ParameterBag($this->parameters));

        /** @var CompilerPassInterface $compilerPass */
        foreach ($this->compilerPasses as $compilerPass) {
            $containerBuilder->addCompilerPass($compilerPass);
        }

        /** @var Extension $extension */
        foreach ($this->extensions as $extension) {
            $extension->load($this->parameters, $containerBuilder);
        }

        foreach ($this->parameters as $key => $value) {
            $containerBuilder->setParameter($key, $value);
        }

        $containerBuilder->compile();

        $this->dumpContainer($containerBuilder);

        return $this->cachedContainer();
    }

    /**
     * @return bool
     */
    public function hasActualContainer(): bool
    {
        if (false === $this->environment->isDebug()) {
            /* @param ConfigCache */
            return true === $this->configCache()->isFresh();
        }

        return false;
    }

    /**
     * @param string $directory
     */
    public function setCacheDirectoryPath(string $directory): void
    {
        $this->cacheDirectory = rtrim($directory, '/');
    }

    /**
     * @param SymfonyContainerBuilder $builder
     */
    private function dumpContainer(SymfonyContainerBuilder $builder): void
    {
        $dumper = new PhpDumper($builder);
        $content = $dumper->dump([
                'class' => $this->getContainerClassName(),
                'base_class' => 'Container',
                'file' => $this->configCache()->getPath(),
            ]
        );

        if (true === \is_string($content)) {
            $this->configCache()->write($content, $builder->getResources());
        }
    }

    /**
     * @return string
     */
    private function getContainerClassName(): string
    {
        return sprintf(
            self::CONTAINER_NAME_TEMPLATE,
            lcfirst('CCET'),
            ucfirst((string) $this->environment)
        );
    }

    /**
     * @return ConfigCache
     */
    private function configCache(): ConfigCache
    {
        if (null === $this->configCache) {
            $this->configCache = new ConfigCache($this->getContainerClassPath(), $this->environment->isDebug());
        }

        return $this->configCache;
    }

    /**
     * @return string
     */
    private function getContainerClassPath(): string
    {
        return sprintf('%s/%s.php', $this->cacheDirectory(), $this->getContainerClassName());
    }

    /**
     * @return string
     */
    private function cacheDirectory(): string
    {
        $cacheDirectory = (string) $this->cacheDirectory;

        if ('' === $cacheDirectory && false === is_writable($cacheDirectory)) {
            $cacheDirectory = sys_get_temp_dir();
        }

        return rtrim($cacheDirectory, '/');
    }

    /**
     * @return ContainerInterface
     */
    private function cachedContainer(): ContainerInterface
    {
        /**
         * @noinspection   PhpIncludeInspection Include generated file
         * @psalm-suppress UnresolvableInclude Include generated file
         */
        include_once $this->getContainerClassPath();

        /** @psalm-var class-string<\Symfony\Component\DependencyInjection\Container> $containerClassName */
        $containerClassName = $this->getContainerClassName();

        $container = new $containerClassName();

        return $container;
    }
}
