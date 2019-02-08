<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Fetcher;

interface ReceiverInterface
{
    public function receive(): void;
}
