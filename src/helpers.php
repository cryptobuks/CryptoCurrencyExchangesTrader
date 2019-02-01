<?php

declare(strict_types=1);

namespace App;

if (!\function_exists('avg')) {
    /**
     * return average of the giving items.
     *
     * @param $items
     *
     * @return float|int|false
     */
    function avg($items)
    {
        //waiting is_countable ;P
        if ($items instanceof \Countable || \is_array($items)) {
            return array_sum($items) / \count($items);
        }

        return false;
    }
}
