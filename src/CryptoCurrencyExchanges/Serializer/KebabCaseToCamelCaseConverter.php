<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Serializer;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

final class KebabCaseToCamelCaseConverter implements NameConverterInterface
{
    /**
     * @param string $propertyName
     *
     * @return string
     */
    public function normalize($propertyName): string
    {
        /* The reason is that with a probability of 100% it will never be used. So just skipped */
        return  $propertyName;
    }

    /**
     * @param string $propertyName
     *
     * @return string
     */
    public function denormalize($propertyName): string
    {
        return lcfirst(implode('', array_map('ucfirst', explode('-', $propertyName))));
    }
}
