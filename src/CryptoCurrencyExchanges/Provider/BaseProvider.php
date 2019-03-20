<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider;

use ReflectionClass;
use ReflectionException;

class BaseProvider
{
    /**
     * Each provider should have a possibility self converted to a string, because they FQCN uses to provide some info.
     * Important thing, if not using late static bindings here, we not found out provider by FQCN in command.
     *
     * @see {\App\Command\SearchProvidersCommand::execute}
     *
     * @throws ReflectionException
     *
     * @return string
     */
    public function __toString(): string
    {
        return (new ReflectionClass(static::class))->getShortName();
    }

    /**
     * @throws ReflectionException
     *
     * @return string An Available provider describe string
     */
    public function describe(): string
    {
        $class = new ReflectionClass(static::class);
        $constantMap = implode(
            ', ',
            array_map(
                static function (string $v, string $k): string {
                    return sprintf('%s="%s"', (string) $k, (string) $v);
                },
                $class->getConstants(),
                array_keys($class->getConstants())
            )
        );

        return sprintf(
            'Your provider information FQCN: "%s", %s',
            $class->getName(),
            $constantMap
            );
    }
}
