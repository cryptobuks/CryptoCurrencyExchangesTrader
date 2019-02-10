<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\Provider\Coinbase\Enum;

class Coinbase
{
    public const AGREE_BTC_AMOUNT_VARIES = 'agree_btc_amount_varies';
    public const COMMIT = 'commit';
    public const ENDING_BEFORE = 'ending_before';
    public const EXPAND = 'expand';
    public const FEE = 'fee';
    public const FETCH_ALL = 'fetch_all';
    public const IDEM = 'idem';
    public const LIMIT = 'limit';
    public const MISPAYMENT = 'mispayment';
    public const ORDER = 'order';
    public const QUOTE = 'quote';
    public const REFUND_ADDRESS = 'refund_address';
    public const SKIP_NOTIFICATIONS = 'skip_notifications';
    public const STARTING_AFTER = 'starting_after';
    public const TWO_FACTOR_TOKEN = 'two_factor_token';

    private function __construct()
    {
    }
}
