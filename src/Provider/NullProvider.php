<?php

declare(strict_types=1);

namespace App\Provider;

final class NullProvider implements ProviderInterface
{
    public function __toString(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    public function test()
    {
        // TODO: Implement test() method.
    }
}
