<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\SearchProvidersCommand;
use App\DependencyInjection\Compiler\ProviderPass;
use App\Provider\ProviderResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SearchProvidersCommandTest extends TestCase
{
    public function testProvidersListWorksCorrectly(): void
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->register(Application::class, new Application());
        $containerBuilder->addCompilerPass(new ProviderPass());
        /** @var Application $application */
        $application = $containerBuilder->get(Application::class);
        $application->addCommands([
            new SearchProvidersCommand(new ProviderResolver()),
        ]);

        $command = $application->find('providers:search');
        $commandTeser = new CommandTester($command);

        $this->assertNotEmpty($command);
        $this->assertInstanceOf(Command::class, $command);
        $commandTeser->execute([
            'command' => $command->getName(),
            'provider' => 'AnotherProvider',
        ]);
    }
}
