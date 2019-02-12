<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Mapper;

interface MapperInterface
{
    /**
     * @param array  $data
     * @param string $className FQCN
     *
     * @return mixed
     */
    public function convert(array $data, string $className);
}
