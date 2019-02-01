#!/usr/bin/env php
<?php

use App\Bootstrap;
use App\Kernel;

include __DIR__ .'/../vendor/autoload.php';

$bootstrap = Bootstrap::withDotEnv(__DIR__.'/../.env')
->registerConsoleCommands()
->enableAutoImportsProviders()
->addParameters([
    'param' => 'value',
    'int' => 123,
    'class' => Kernel::class,
])
->provideParametersPath(__DIR__.'/config.php');
$container = $bootstrap->boot();
$kernel = (new Kernel($container))
    ->enableSentryErrorHandler()
    ->runInConsoleMode();
