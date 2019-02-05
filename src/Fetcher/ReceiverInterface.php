<?php

declare(strict_types=1);

namespace App\Fetcher;

interface ReceiverInterface
{
    public function receive(): void;
}
