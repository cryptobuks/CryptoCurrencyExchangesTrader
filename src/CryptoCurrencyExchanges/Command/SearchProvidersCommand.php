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

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @psalm-suppress InvalidCast
     * @psalm-suppress MixedAssignment
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $computedArgument = (string) $input->getArgument('provider');

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
     * @param string $computedArgument
     * @psalm-suppress MixedArgument
     *
     * @return bool
     */
    private function checkTypos(string $parsedProvider, string $computedArgument): bool
    {
        return false !== mb_strpos($parsedProvider, $computedArgument) || levenshtein(
                (string) $computedArgument,
                $parsedProvider
            ) <= mb_strlen((string) $computedArgument) / 3;
    }

    /**
     * @param string $needle
     * @param array  $haystack
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedAssignment
     * @psalm-suppress PossiblyInvalidArrayOffset
     *
     * @return string
     */
    private function providePayload(string $needle, array $haystack): string
    {
        $haystackProviders = array_map('strval', $haystack);

        $key = array_search($needle, $haystackProviders, true);

        /** @var ProviderInterface $computedProvider */
        $computedProvider = $haystack[$key];

        return $computedProvider->describe() ?? '';
    }
}
