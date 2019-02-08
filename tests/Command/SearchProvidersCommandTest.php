<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Tests\Command;

use Kefzce\CryptoCurrencyExchanges\Command\SearchProvidersCommand;
use Kefzce\CryptoCurrencyExchanges\DependencyInjection\Compiler\ProviderPass;
use Kefzce\CryptoCurrencyExchanges\Provider\NullProvider;
use Kefzce\CryptoCurrencyExchanges\Provider\ProviderResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SearchProvidersCommandTest extends TestCase
{
    /**
     * @dataProvider providerList
     *
     * @param ProviderInterface $provider
     *
     * @throws \Exception
     */
    public function testProvidersListWorksCorrectly($provider): void
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->register(Application::class, new Application());
        $containerBuilder->addCompilerPass(new ProviderPass());
        /** @var Application $application */
        $application = $containerBuilder->get(Application::class);
        $resolverInstance = new ProviderResolver();
        $resolverInstance->addProvider($provider);
        $application->addCommands([
            new SearchProvidersCommand($resolverInstance),
        ]);

        $command = $application->find('providers:search');
        $commandTeser = new CommandTester($command);

        $this->assertNotEmpty($command);
        $this->assertInstanceOf(Command::class, $command);
        $commandTeser->execute([
            'command' => $command->getName(),
            'provider' => 'n',
        ]);

        $output = $commandTeser->getDisplay();

        $r = new \ReflectionClass($provider);

        $this->assertNotEmpty($output);

        $this->assertSame(
            trim(
                sprintf(
                    '[WARNING] Provider "n" not found... Did you mean "%s" ?              
                            providers:list to see all available providers',
                    $r->getShortName(),
                )
            ),
            trim($output)
        );
    }

    /**
     * @noinspection PhpUndefinedClassInspection
     *
     * @return \Generator|null
     */
    public function providerList(): ?\Generator
    {
        yield [new NullProvider()];
    }
}
