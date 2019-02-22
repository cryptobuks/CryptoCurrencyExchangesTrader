<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Validator;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class Validator
{
    /**
     * @return \Symfony\Component\Validator\Validator\ValidatorInterface
     */
    public static function create(): ValidatorInterface
    {
        return Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();
    }
}
