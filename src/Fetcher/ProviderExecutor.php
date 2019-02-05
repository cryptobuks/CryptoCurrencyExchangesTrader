<?php

declare(strict_types=1);

namespace App\Fetcher;

use Symfony\Component\DependencyInjection\ContainerInterface;

final class ProviderExecutor implements ReceiverInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function receive(): void
    {
    }
}
