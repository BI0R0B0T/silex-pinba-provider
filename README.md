# silex-pinba-provider #
Provide [PinbaBundle](https://github.com/intaro/pinba-bundle) functionality for [Silex](http://silex.sensiolabs.org/)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/BI0R0B0T/silex-pinba-provider/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/BI0R0B0T/silex-pinba-provider/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/BI0R0B0T/silex-pinba-provider/badges/build.png?b=master)](https://scrutinizer-ci.com/g/BI0R0B0T/silex-pinba-provider/build-status/master)

## Installation ##

### For Silex 2.0

```bash
    composer require bi0r0b0t/silex-pinba-provider
```
or
Require the bundle in your `composer.json` file:

```json
{
    "require": {
        "bi0r0b0t/silex-pinba-provider": "~1.0",
    }
}
```

### For Silex 1.0
Through [Composer](http://getcomposer.org) as [bi0r0b0t/silex-pinba-provider][1] .

Require the bundle in your `composer.json` file:

```json
{
    "require": {
        "bi0r0b0t/silex-pinba-provider": "~0.1",
    }
}
```

## Register ##

**Important!** Register the bundle in `prod` environment after TwigServiceProvider and DoctrineServiceProvider:

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

## Using with console ##

```php
/**
 * @var $console Symfony\Component\Console\Application
 * @var $app     Silex\Application
 */
if (function_exists('pinba_script_name_set')) {
    pinba_script_name_set('console');
    $input = new Symfony\Component\Console\Input\ArgvInput();
    $timer = pinba_timer_start(array(
        'server'  => $app['intaro_pinba.server.name'],
        'group'   => 'console::run',
        'command' => $input->getFirstArgument(),
    ));
    $res = $console->run($input);
    pinba_timer_stop($timer);
    return $res;
} else {
    return $console->run();
}
```

[1]: https://packagist.org/packages/bi0r0b0t/silex-pinba-provider
[2]: https://packagist.org/packages/dflydev/doctrine-orm-service-provider