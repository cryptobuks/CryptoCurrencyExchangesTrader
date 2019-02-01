<?php

declare(strict_types=1);

namespace App\Provider;

interface ProviderInterface
{
    public const SERVICE_TAG = 'exchanges.provider';

    public function test();
}
