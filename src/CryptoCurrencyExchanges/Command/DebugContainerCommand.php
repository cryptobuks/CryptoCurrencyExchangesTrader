<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DebugContainerCommand extends Command
{
    public static $defaultName = 'debug:container';

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /** @var array */
    private $configs = [];

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    public function configure(): void
    {
        $this->addArgument('property', InputArgument::OPTIONAL, 'Mode of container debug available: services,parameters', 'parameters');
        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \ReflectionException
     *
     * @return mixed
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('property');
        $this->applyWorkMode([
            'workmode' => $type,
        ]);

        if (!$this->container->hasParameter('ccet_environment')) {
            return;
        }

        if ($this->container->getParameter('ccet_environment') !== 'test') {
            throw new \RuntimeException(
                sprintf(
                    'Unable to use this command "%s" in current environment "%s", only available on "test"',
                    \get_class($this),
                    $this->container->getParameter('ccet_environment'),
                ));
        }

        $r = new \ReflectionClass($this->container);
        $property = $r->getProperty($type);
        $property->setAccessible(true);
        $fields = $property->getValue($this->container);

        if (\function_exists('dd')) {
            /* @noinspection ForgottenDebugOutputInspection */
            dd($fields);
        }
        /* @noinspection ForgottenDebugOutputInspection */
        var_dump($fields);
        die();
    }

    /**
     * @param array $configs
     */
    private function applyWorkMode($configs = []): void
    {
        $resolver = new OptionsResolver();

        $resolver
            ->setRequired(['workmode'])
            ->setAllowedTypes('workmode', ['string'])
            ->setAllowedValues('workmode', ['parameters', 'services']);

        $this->configs = $resolver->resolve($configs);
    }
}
