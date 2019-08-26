<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Converter;

interface ConverterInterface
{
    /**
     * @param array  $data
     * @param string $className FQCN
     *
     * @return mixed
     */
    public function convert(array $data, string $className);
}
