# Crypto Currency Exchanges Trader  [![CircleCI](https://travis-ci.com/kefzce/CryptoCurrencyExchangesTrader.svg?branch=master)](https://github.com/kefzce/CryptoCurrencyExchangesTrader) 

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kefzce/CryptoCurrencyExchangesTrader/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kefzce/CryptoCurrencyExchangesTrader/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/kefzce/CryptoCurrencyExchangesTrader/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/kefzce/CryptoCurrencyExchangesTrader/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/kefzce/cli-parser/v/stable)](https://github.com/kefzce/CryptoCurrencyExchangesTrader) [![Total Downloads](https://poser.pugx.org/kefzce/cli-parser/downloads)](https://github.com/kefzce/CryptoCurrencyExchangesTrader) [![Latest Unstable Version](https://poser.pugx.org/kefzce/cli-parser/v/unstable)](https://github.com/kefzce/CryptoCurrencyExchangesTrader) [![License](https://poser.pugx.org/kefzce/cli-parser/license)](https://github.com/kefzce/CryptoCurrencyExchangesTrader) [![composer.lock](https://poser.pugx.org/kefzce/cli-parser/composerlock)](https://github.com/kefzce/CryptoCurrencyExchangesTrader)


# Booting into console mode
```php
#!/usr/bin/env php
<?php

// import namespace
use Kefzce\CryptoCurrencyExchanges\Bootstrap;
use Kefzce\CryptoCurrencyExchanges\Kernel;

include_once __DIR__ .'/../vendor/autoload.php'; // boot autoloader

$bootstrap = Bootstrap::withDotEnv(__DIR__) //specify .env folder
->registerConsoleCommands() //if you wanna boot into console mode provide a few command 
->enableAutoImportsProviders() // required things, register all providers into DependencyInjection Container
->addParameters([
    'param' => 'an example of values',
]) // also you can pass into container additional Parameters
->provideParametersPath(__DIR__.'/config.php'); // or even specify parameters file, which should be simple an array on configuration e.g return []
$container = $bootstrap->boot(); // fetching container
$kernel = (new Kernel($container)) // create kernel and passing container into 
    ->enableSentryErrorHandler() // some additional staff which enable sentry error handling(required sentry dsn)
    ->runInConsoleMode(); //booting application into console mode
```

# Using a single Provider
To start using single provider, just manually create them
e.g
```php
<?php

include_once __DIR__ .'/../vendor/autoload.php'; // boot autoloader

$provider = new \Kefzce\CryptoCurrencyExchanges\Provider\AnotherProvider(); //and passing an additional config into constructor
```


or even with ProviderBuilder:

```php
<?php

include_once __DIR__ .'/../vendor/autoload.php'; // boot autoloader

$computedProvider = (new ProviderBuilder())->build(SomeProvider::class); // SomeProvider
```