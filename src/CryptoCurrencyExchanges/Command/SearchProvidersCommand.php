<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Command;

use Kefzce\CryptoCurrencyExchanges\Provider\ProviderInterface;
use Kefzce\CryptoCurrencyExchanges\Provider\ProviderResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SearchProvidersCommand extends Command
{
    protected static $defaultName = 'providers:search';

    /**
     * @var \Kefzce\CryptoCurrencyExchanges\Provider\ProviderResolver
     */
    private $resolver;

    /**
     * @param \Kefzce\CryptoCurrencyExchanges\Provider\ProviderResolver $resolver
     */
    public function __construct(ProviderResolver $resolver)
    {
        $this->resolver = $resolver;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Search providers with autosuggestion, print nice info');
        $this->addArgument(
            'provider',
            InputArgument::REQUIRED, 'Name of the search provider e.g Short Name of FQCN'
        );
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $computedArgument = $input->getArgument('provider');

        $candidates = [];

        /** @param array|ProviderInterface[] $availableProviders */
        $availableProviders = $this->resolver->getProviders();

        foreach ($availableProviders as $provider) {
            $parsedProvider = mb_strtolower((string) $provider);

            if ($this->checkTypos($parsedProvider, $computedArgument)) {
                $candidates[] = (string) $provider;
            }
        }

        if (!empty($candidates)) {
            $message = sprintf(
                'Provider "%s" not found... Did you mean "%s" ? 
                 providers:list to see all available providers',
                (string) $computedArgument,
                implode('", "', $candidates)
            );

            $same = $candidates[0] === $computedArgument;
            $same ? $io->success(
                $this->providePayload((string) $computedArgument, $availableProviders)
            ) : $io->warning($message);
        }
    }

    /**
     * @param string $parsedProvider
     * @param $computedArgument
     *
     * @return bool
     */
    private function checkTypos(string $parsedProvider, $computedArgument): bool
    {
        return false !== mb_strpos($parsedProvider, $computedArgument) || levenshtein(
                (string) $computedArgument,
                $parsedProvider
            ) <= mb_strlen((string) $computedArgument) / 3;
    }

    /**
     * @param string $needle
     * @param $haystack
     *
     * @return string
     */
    private function providePayload(string $needle, $haystack): string
    {
        $haystackProviders = array_map('strval', $haystack);

        $key = array_search($needle, $haystackProviders, true);

        /** @var ProviderInterface|\Kefzce\Command\Configurable $computedProvider */
        $computedProvider = $haystack[$key];

        return $computedProvider->describe();
    }
}
