<?php

declare(strict_types=1);

namespace App\Http;

use GuzzleHttp\Client;

interface HttpBuilderInterface
{
    /**
     * @param array<array-key, mixed> $options
     *
     * @return Client
     */
    public static function build(array $options = []): Client;
}
