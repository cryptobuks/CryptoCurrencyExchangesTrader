<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges;

use Raven_Autoloader;
use Raven_Client;
use Raven_ErrorHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class Kernel
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string|null $dsn
     *
     * @return Kernel
     */
    public function enableSentryErrorHandler(string $dsn = null): self
    {
        Raven_Autoloader::register();
        $dsn = $dsn ?? getenv('SENTRY_DSN');
        $client = new Raven_Client($dsn);
        $errorHandler = new Raven_ErrorHandler($client);
        $errorHandler->registerExceptionHandler();
        $errorHandler->registerErrorHandler();
        $errorHandler->registerShutdownFunction();

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function runInConsoleMode(): void
    {
        if ($this->container->has('shell.console')) {
            /** @var \Symfony\Component\Console\Application $application */
            $application = $this->container->get('shell.console');
            $application->run();
        }
    }

    /**
     * @param string $service
     * @psalm-suppress MixedAssignment
     *
     * @return object
     *
     * @deprecated since 1.0
     */
    public function getKernelContainerService(string $service): object
    {
        $object = $this->container->get($service);

        return $object;
    }

    /**
     * @param string $service
     * @psalm-suppress MixedAssignment
     *
     * @return mixed
     *
     * @deprecated since 1.0
     */
    public function getKernelParameterService(string $service)
    {
        $object = $this->container->getParameter($service);

        return $object;
    }
}
