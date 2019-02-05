<?php

declare(strict_types=1);

namespace App;

if (!\function_exists('avg')) {
    /**
     * return average of the giving items.
     *
     * @param array<array-key, mixed> $items
     *
     * @return float|int|false
     */
    function avg($items)
    {
        if (is_countable($items)) {
            return array_sum($items) / \count($items);
        }

        return false;
    }

    /**
     * @param string $path
     */
    function removeDirectory(string $path): void
    {
        $files = glob(preg_replace('/(\*|\?|\[)/', '[$1]', $path) . '/{,.}*', GLOB_BRACE);

        foreach ($files as $file) {
            if ($file === $path . '/.' || $file === $path . '/..') {
                continue;
            }
            is_dir($file) ? removeDirectory($file) : unlink($file);
        }
        rmdir($path);
    }
}
