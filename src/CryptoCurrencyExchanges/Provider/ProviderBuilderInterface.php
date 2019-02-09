<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider;

interface ProviderBuilderInterface
{
    /**
     * @param string $className FQCN
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function build(string $className);
}
