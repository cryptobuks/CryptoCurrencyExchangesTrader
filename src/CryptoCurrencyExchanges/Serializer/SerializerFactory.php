<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Serializer;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class SerializerFactory
{
    /**
     * @return \Symfony\Component\Serializer\SerializerInterface
     */
    public static function createSerializer(): SerializerInterface
    {
        $objectNormalizer = new ObjectNormalizer(
            null,
            new CamelCaseToSnakeCaseNameConverter(),
            null,
            new PhpDocExtractor()
        );

        return new Serializer([$objectNormalizer], [new JsonEncoder()]);
    }
}
