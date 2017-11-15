<?php
/**
 * @author Mikhail Dolgov <dolgov@bk.ru>
 * @date   04.03.2016 15:27
 */

namespace SilexPinbaProvider;


use Doctrine\DBAL\Configuration;
use Intaro\PinbaBundle\Logger\DbalLogger;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;

class SilexPinbaProvider implements ServiceProviderInterface, BootableProviderInterface
{

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     * @param Container $app
     */
    public function register(Container $app)
    {

        $app['intaro_pinba.script_name_configure.class']      = 'Intaro\PinbaBundle\EventListener\ScriptNameConfigureListener';
        $app['intaro_pinba.stopwatch.class']                  = 'Intaro\PinbaBundle\Stopwatch\Stopwatch';
        $app['intaro_pinba.templating.engine.twig.class']     = 'SilexPinbaProvider\Twig\TimedTwigDecorator';
        $app['intaro_pinba.dbal.logger.class']                = 'Intaro\PinbaBundle\Logger\DbalLogger';
        $app['intaro_pinba.server.name']                      = 'localhost';
        $app['intaro_pinba.script_name_configure.enable']     = true;


        $app['intaro_pinba.script_name_configure.listener'] = function () use ($app) {
            return new $app['intaro_pinba.script_name_configure.class'];
        };

        $app['intaro_pinba.stopwatch'] = function () use ($app) {
            return new $app['intaro_pinba.stopwatch.class'];
        };

        $app['doctrine.dbal.logger'] = function () use ($app) {
            /**
             * @see \Intaro\PinbaBundle\Logger\DbalLogger
             */
            $className = $app['intaro_pinba.dbal.logger.class'];
            $host = isset($app["intaro_pinba.doctrine.database_host"]) ? $app["intaro_pinba.doctrine.database_host"] : $app['intaro_pinba.server.name'];
            return new $className($app["intaro_pinba.stopwatch"], $host);
        };

        $app['dbs.config'] = function ($app) {
            $app['dbs.options.initializer']();

            $configs = new Container();
            foreach ($app['dbs.options'] as $name => $options) {
                $configs[$name] = new Configuration();

                if (isset($app['logger']) && class_exists('Intaro\PinbaBundle\Logger\DbalLogger')) {
                    $configs[$name]->setSQLLogger($app['doctrine.dbal.logger']);
                }
            }

            return $configs;
        };
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     * @param Application $app
     */
    public function boot(Application $app)
    {
        $app['twig'] = $app->extend('twig', function (\Twig_Environment $twig) use ($app) {
            /**
             * @see \Intaro\PinbaBundle\Twig\TimedTwigEngine
             */
            $className = $app['intaro_pinba.templating.engine.twig.class'];
            return new $className($twig, $app['intaro_pinba.stopwatch'], $app['intaro_pinba.server.name']);
        });

        if (!function_exists('pinba_script_name_set') || PHP_SAPI === 'cli' || !$app['intaro_pinba.script_name_configure.enable']) {
            return;
        }
        /**
         * @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface
         */
        $dispatcher = $app['dispatcher'];
        $dispatcher->addListener('kernel.request', array($app['intaro_pinba.script_name_configure.listener'], 'onRequest'));

    }

}