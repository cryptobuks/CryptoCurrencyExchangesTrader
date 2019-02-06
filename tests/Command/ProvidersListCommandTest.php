<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\ListProvidersCommand;
use App\Provider\ProviderResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ProvidersListCommandTest extends TestCase
{
    public function testProvidersListWorksCorrectly(): void
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->register(Application::class, new Application());
        /** @var Application $application */
        $application = $containerBuilder->get(Application::class);
        $application->addCommands([
            new ListProvidersCommand(new ProviderResolver()),
        ]);

        $command = $application->find('providers:list');
        $commandTeser = new CommandTester($command);

        $commandTeser->execute([
            'command' => $command->getName(),
        ]);

        $output = $commandTeser->getDisplay();

        $this->assertContains('[OK] You lookup at providers:', $output);
    }
}
