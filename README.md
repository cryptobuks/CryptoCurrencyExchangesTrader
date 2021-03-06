# Crypto Currency Exchanges Trader  [![CircleCI](https://travis-ci.com/kefzce/CryptoCurrencyExchangesTrader.svg?branch=master)](https://github.com/kefzce/CryptoCurrencyExchangesTrader) 

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kefzce/CryptoCurrencyExchangesTrader/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kefzce/CryptoCurrencyExchangesTrader/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/kefzce/CryptoCurrencyExchangesTrader/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/kefzce/CryptoCurrencyExchangesTrader/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/systemfailure/crypto-currency-exchanges-trader/v/stable)](https://packagist.org/packages/systemfailure/crypto-currency-exchanges-trader)
[![composer.lock](https://poser.pugx.org/systemfailure/crypto-currency-exchanges-trader/composerlock)](https://packagist.org/packages/systemfailure/crypto-currency-exchanges-trader)
[![Total Downloads](https://poser.pugx.org/systemfailure/crypto-currency-exchanges-trader/downloads)](https://packagist.org/packages/systemfailure/crypto-currency-exchanges-trader)
[![Latest Unstable Version](https://poser.pugx.org/systemfailure/crypto-currency-exchanges-trader/v/unstable)](https://packagist.org/packages/systemfailure/crypto-currency-exchanges-trader)
[![License](https://poser.pugx.org/systemfailure/crypto-currency-exchanges-trader/license)](https://packagist.org/packages/systemfailure/crypto-currency-exchanges-trader)
# Installation
```bash
composer require systemfailure/crypto-currency-exchanges-trader
```

# Symfony Bundle
> Not required, you can use it w/o framework

Add folowing lines into config/bundles.php
```php
return [
    // ... Another bundles
    Kefzce\CryptoCurrencyExchanges\KefzceCryptoCurrencyExchangesBundle::class => ['all' => true],
];
```
# Framework independent usage
### As the single Provider w/o Kernel
> Important! Provider should be accessible from outside via [container configuration](https://github.com/kefzce/CryptoCurrencyExchangesTrader/blob/master/src/CryptoCurrencyExchanges/Resources/services.yaml#L5) / [explanation](https://symfony.com/blog/new-in-symfony-3-4-services-are-private-by-default)
```php
<?php
// import namespace
use Kefzce\CryptoCurrencyExchanges\Bootstrap;

$loader = require __DIR__.'/../vendor/autoload.php'; // boot autoloader

$bootstrap = Bootstrap::withDotEnv(__DIR__) //specify .env folder or use ::withEnvironmentValues() 
->registerLoader($loader) // Annotation registry needs specify composer autoload
->enableAutoImportsProviders(); // required thing, register all providers into DependencyInjection Container
$container = $bootstrap->boot(); // fetching container
$computedProvider = $container->get(SomeProvider::class); //SomeProvider instance
```


### With ProviderBuilder:
> Important! Provider should be accessible from outside via [container configuration](https://github.com/kefzce/CryptoCurrencyExchangesTrader/blob/master/src/CryptoCurrencyExchanges/Resources/services.yaml#L5) / [explanation](https://symfony.com/blog/new-in-symfony-3-4-services-are-private-by-default)
```php
<?php
$loader = require __DIR__.'/../vendor/autoload.php'; // boot autoloader

$bootstrap = Bootstrap::withDotEnv(__DIR__) //specify .env folder or use ::withEnvironmentValues()
->registerLoader($loader) // Annotation registry needs specify composer autoload 
->enableAutoImportsProviders(); // required thing, register all providers into DependencyInjection Container
$container = $bootstrap->boot(); // fetching container
/** ProviderBuilder $builder */
$builder = $container->get(ProviderBuilder::class); // an instance of ProviderBuilder

$computedProvider =  $builder->build(SomeProvider::class); // instance of SomeProvider
// Same as
$computedProvider = $builder::build(FQCN::class);
// $computedProvider variable contains ready to work an instance of required Provider.
// or even
$computedProvider = (new ProviderBuilder($container))->build(SomeProvider::class);
```

# List of all available Providers:
> Required console mode OR installing via Symfony bundle
```bash
php bin/console providers:list
```
# Search provider by FQCN:
> Required console mode OR installing via Symfony bundle

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

$loader = require __DIR__.'/../vendor/autoload.php'; // boot autoloader

$bootstrap = Bootstrap::withDotEnv(__DIR__)//specify .env folder or use ::withEnvironmentValues() 
->registerLoader($loader) // Annotation registry needs specify composer autoload
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
# Symfony Usage:
### Method injection
```php
<?php
class Controller 
{
    public function method(SomeProvider $provider)
    {
        $provider->doSome(); // where $provider ready to work instance
    }
}
```
### Constructor injection
```php
<?php
class Service 
{
    /** @var ProviderInterface */
    private $provider;
    
    public function __construct(SomeProvider $provider) 
    {
        $this->provider = $provider;
    }
    
    public function method()
    {
        $this->provider->doSome();
    }
}
```
### Service Locator
```php
<?php
class Service
{
    public function __construct(ContainerInterface $container) 
    {
        $provider = $container->get(SomeProvider::class);
        // or even
        $provider = $this->get(SomeProvider::class);
        // same as 
        $this->container->get(SomeProvider::class);
        // if you extend AbstractController (please dont do this)
    }
}
```
## Coinbase Usage Example
## Usage

This is not intended to provide complete documentation of the API. For more
detail, please refer to the
[official documentation](https://developers.coinbase.com/api/v2).

### [Market Data](https://developers.coinbase.com/api/v2#data-api)

**List supported native currencies**

```php
/** @var CurrenciesResource $currencies */
$currencies = $provider->getCurrencies();
```

**List exchange rates**

```php
/** @var ExchangeRatesResource $rates */
$rates = $provider->getExchangeRates();
```

**Buy price**

```php
/** @var BuyPriceCurrencyResource $buyPrice */
$buyPrice = $provider->getBuyPrice('USD');
```

**Sell price**

```php
/** @var SellPriceCurrencyResource $sellPrice */
$sellPrice = $provider->getSellPrice('USD');
```

**Spot price**

```php
/** @var SpotPriceCurrencyResource $spotPrice */
$spotPrice = $provider->getSpotPrice('USD');
```

**Current server time**

```php
/** @var CurrentServiceTimeResource $time */
$time = $provider->getCurrentServiceTime();
```

### [Users](https://developers.coinbase.com/api/v2#users)

**Get authorization info**

```php
/** @var CurrentAuthorizationResource $auth */
$auth = $provider->getCurrentAuthorization();
```

**Lookup user info**

```php
/** @var UserResource $user */
$user = $provider->getUser($userId);
```
### 
# Run tests
> All test available in tests folder, run them directly by typing
```bash
composer test
```
