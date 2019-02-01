<?php

declare(strict_types=1);

namespace App;

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
            $application = $this->container->get('shell.console');
            $application->run();
        }
    }
}
