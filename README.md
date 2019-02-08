# Crypto Currency Exchanges Trader  [![CircleCI](https://travis-ci.com/kefzce/CryptoCurrencyExchangesTrader.svg?branch=master)](https://github.com/kefzce/CryptoCurrencyExchangesTrader) 

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kefzce/CryptoCurrencyExchangesTrader/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kefzce/CryptoCurrencyExchangesTrader/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/kefzce/CryptoCurrencyExchangesTrader/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/kefzce/CryptoCurrencyExchangesTrader/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/kefzce/cli-parser/v/stable)](https://github.com/kefzce/CryptoCurrencyExchangesTrader) [![Total Downloads](https://poser.pugx.org/kefzce/cli-parser/downloads)](https://github.com/kefzce/CryptoCurrencyExchangesTrader) [![Latest Unstable Version](https://poser.pugx.org/kefzce/cli-parser/v/unstable)](https://github.com/kefzce/CryptoCurrencyExchangesTrader) [![License](https://poser.pugx.org/kefzce/cli-parser/license)](https://github.com/kefzce/CryptoCurrencyExchangesTrader) [![composer.lock](https://poser.pugx.org/kefzce/cli-parser/composerlock)](https://github.com/kefzce/CryptoCurrencyExchangesTrader)

# Installation
```bash
composer require systemfailure/crypto-currency-exchanges-trader
```
# Using a single Provider w/o creating Kernel
> Important! Provider should be accessible from outside via [container configuration](https://github.com/kefzce/CryptoCurrencyExchangesTrader/blob/master/src/CryptoCurrencyExchanges/Resources/services.yaml#L5) / [explanation](https://symfony.com/blog/new-in-symfony-3-4-services-are-private-by-default)
```php
<?php
// import namespace
use Kefzce\CryptoCurrencyExchanges\Bootstrap;

include_once __DIR__ . 'vendor/autoload.php'; // boot autoloader

$bootstrap = Bootstrap::withDotEnv(__DIR__) //specify .env folder or use ::withEnvironmentValues() 
->enableAutoImportsProviders(); // required thing, register all providers into DependencyInjection Container
$container = $bootstrap->boot(); // fetching container
$computedProvider = $container->get(SomeProvider::class); //SomeProvider instance
```


# With ProviderBuilder:
```php
<?php
include_once __DIR__ . 'vendor/autoload.php'; // boot autoloader

$computedProvider = (new ProviderBuilder())->build(SomeProvider::class); 
// Same as
$computedProvider = ProviderBuilder::build(FQCN::class);
// $computedProvider variable contains ready to work an instance of required Provider.
```

# List of all available Providers:
> After booting in console mode OR adding KefzceCryptoCurrencyExchangesBundle::class => ['all' => true], into bundles.php (Symfony)
```bash
php bin/console providers:list
```
# Search provider by FQCN:
> After booting in console mode OR adding KefzceCryptoCurrencyExchangesBundle::class => ['all' => true], into bundles.php (Symfony)

```bash
php bin/console providers:search ProviderName // give some nice output information about provider
```
# Booting into console mode
> Not required if you using this package with Symfony framework
```php
<?php
// import namespace
use Kefzce\CryptoCurrencyExchanges\Bootstrap;
use Kefzce\CryptoCurrencyExchanges\Kernel;

include_once __DIR__ . 'vendor/autoload.php'; // boot autoloader

$bootstrap = Bootstrap::withDotEnv(__DIR__) //specify .env folder or use ::withEnvironmentValues() 
->registerConsoleCommands() //if you wanna boot into console mode provide a few commands
->enableAutoImportsProviders() // required thing, register all providers into DependencyInjection Container
->addParameters([
    'param' => 'an example of values',
]) // also you can pass into container additional Parameters
->provideParametersPath(__DIR__.'/config.php'); // or even specify parameters file, which should be simple an array on configuration e.g return []
$container = $bootstrap->boot(); // fetching container
$kernel = (new Kernel($container)) // create kernel and passing container into 
    ->enableSentryErrorHandler() // some additional staff which enable sentry error handling(required sentry dsn)
    ->runInConsoleMode(); //booting application into console mode
```

# Run tests
> All test available in tests folder, run them directly by typing
```bash
composer test
```
