<?php

declare(strict_types=1);

namespace App\Fetcher;

interface ReceiverInterface
{
    /**
     * @return mixed
     */
    public function receive();
}
