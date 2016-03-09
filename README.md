# silex-pinba-provider #
Provide [PinbaBundle](https://github.com/intaro/pinba-bundle) functionality for [Silex](http://silex.sensiolabs.org/)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/BI0R0B0T/silex-pinba-provider/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/BI0R0B0T/silex-pinba-provider/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/BI0R0B0T/silex-pinba-provider/badges/build.png?b=master)](https://scrutinizer-ci.com/g/BI0R0B0T/silex-pinba-provider/build-status/master)

## Installation ##
Through [Composer](http://getcomposer.org) as [bi0r0b0t/silex-pinba-provider][1] .

Require the bundle in your `composer.json` file:

````json
{
    "require": {
        "bi0r0b0t/silex-pinba-provider": "~0.1.0",
    }
}
```

## Register ##

**Important!** Register the bundle in `prod` environment:

```php
/**
 * @var $app Silex\Application
 */
$app->register(new SilexPinbaProvider\SilexPinbaProvider()
```

## Using with Doctrine ORM ##

Examples based on [dflydev/doctrine-orm-service-provider][2]

### Collecting Filesystem metrics ###

```php
/**
 * @var $app Silex\Application
 */
$app['orm.cache.factory.filesystem'] = $app->protect(function($cacheOptions) use ($app) {
    if (empty($cacheOptions['path'])) {
        throw new \RuntimeException('FilesystemCache path not defined');
    }
    $cache = new  SilexPinbaProvider\Cache\Filesystem($cacheOptions['path']);
    $cache->setStopwatch($app['intaro_pinba.stopwatch']);
    return $cache;
});
```        

### Collecting Memcache metrics ###

```php
/**
 * @var $app Silex\Application
 */
$app['orm.cache.factory.backing_memcache'] = $app->protect(function() use ($app) {
   $cache = new Intaro\PinbaBundle\Cache\Memcache();
   $cache->setStopwatch($app['intaro_pinba.stopwatch']);
   return $cache;
});
```

[1]: https://packagist.org/packages/bi0r0b0t/silex-pinba-provider
[2]: https://packagist.org/packages/dflydev/doctrine-orm-service-provider