<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider;

use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

interface ProviderBuilderInterface
{
    /**
     * @param string $className FQCN
     *
     * @throws ServiceNotFoundException
     */
    public function build(string $className);
}
