<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Tests\Command;

use Kefzce\CryptoCurrencyExchanges\Command\DebugContainerCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DebugContainerCommandTest extends TestCase
{
    public function testDebugContainerWorkCorrectlyOnTestEnv(): void
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setParameter('ccet_environment', 'test');
        $containerBuilder->register(Application::class, new Application());
        /** @var Application $application */
        $application = $containerBuilder->get(Application::class);
        $application->addCommands([
            new DebugContainerCommand($containerBuilder),
        ]);

        $command = $application->find('debug:container');
        $this->assertNotEmpty($command);
        $commandTeser = new CommandTester($command);

        $commandTeser->execute([
            'command' => $command->getName(),
            'property' => 'services',
        ]);

        $output = $commandTeser->getDisplay();

        $this->assertNotEmpty($output);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testDebugContainerShouldThrowExceptionIfRunningOnIncorrectEnvironment(): void
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setParameter('ccet_environment', 'prod');
        $containerBuilder->register(Application::class, new Application());
        /** @var Application $application */
        $application = $containerBuilder->get(Application::class);
        $application->addCommands([
            new DebugContainerCommand($containerBuilder),
        ]);

        $command = $application->find('debug:container');
        $this->assertNotEmpty($command);
        $commandTeser = new CommandTester($command);

        $commandTeser->execute([
            'command' => $command->getName(),
            'property' => 'services',
        ]);

        $output = $commandTeser->getDisplay();

        $this->assertNotEmpty($output);
    }
}
