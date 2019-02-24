<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Command;

use Kefzce\CryptoCurrencyExchanges\Provider\ProviderInterface;
use Kefzce\CryptoCurrencyExchanges\Provider\ProviderResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ListProvidersCommand extends Command
{
    protected static $defaultName = 'providers:list';

    /**
     * @var \Kefzce\CryptoCurrencyExchanges\Provider\ProviderResolver
     */
    private $resolver;

    /**
     * @param \Kefzce\CryptoCurrencyExchanges\Provider\ProviderResolver $resolver
     */
    public function __construct(ProviderResolver $resolver)
    {
        parent::__construct();
        $this->resolver = $resolver;
    }

    protected function configure(): void
    {
        $this->setDescription('Print list providers');
        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        /** @var ProviderInterface[] $providers */
        $providers = $this->resolver->getProviders();
        $io->success(
            sprintf(
                'You look up at providers ↓ (%d)',
            is_countable($providers) ? \count($providers) : 0
            )
        );
        $io->listing(
            array_map(
                function (ProviderInterface $value): string {
                    return (new \ReflectionClass($value))->getShortName();
                }, $providers)
        );
    }
}
