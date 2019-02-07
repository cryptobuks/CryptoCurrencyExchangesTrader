<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Command;

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
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $providers = $this->resolver->getProviders();
        $io->success(
            sprintf(
                'You lookup at providers: %s',
                implode(', ', array_map('\get_class', $providers)))
        );
    }
}
