<?php
/**
 * @author Mikhail Dolgov <dolgov@bk.ru>
 * @date   04.03.2016 15:27
 */

namespace SilexPinbaProvider;


use Doctrine\DBAL\Configuration;
use Intaro\PinbaBundle\Logger\DbalLogger;
use Silex\Application;
use Silex\ServiceProviderInterface;

class SilexPinbaProvider implements ServiceProviderInterface{

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     * @param Application $app
     */
    public function register(Application $app) {
        if( !function_exists('pinba_script_name_set') || PHP_SAPI === 'cli' )
        {
            return;
        }

        $app['intaro_pinba.script_name_configure.class']      = 'Intaro\PinbaBundle\EventListener\ScriptNameConfigureListener';
        $app['intaro_pinba.stopwatch.class']                  = 'Intaro\PinbaBundle\Stopwatch\Stopwatch';
        $app['intaro_pinba.templating.engine.twig.class']     = 'SilexPinbaProvider\Twig\TimedTwigDecorator';
        $app['intaro_pinba.dbal.logger.class']                = 'Intaro\PinbaBundle\Logger\DbalLogger';


        $app['intaro_pinba.script_name_configure.listener'] = $app->share(function() use($app) {
            return new $app['intaro_pinba.script_name_configure.class'];
        });

        $app['intaro_pinba.stopwatch']  = $app->share(function() use ($app) {
            return new $app['intaro_pinba.stopwatch.class'];
        });

        $app['doctrine.dbal.logger'] = $app->share(function() use ($app) {
            /**
             * @see \Intaro\PinbaBundle\Logger\DbalLogger
             */
            $className = $app['intaro_pinba.dbal.logger.class'];
            $host      = isset($app["intaro_pinba.doctrine.database_host"]) ? $app["intaro_pinba.doctrine.database_host"] : 'localhost';
            return new $className( $app["intaro_pinba.stopwatch"], $host);
        });

        $app['dbs.config'] = $app->share(function ($app) {
            $app['dbs.options.initializer']();

            $configs = new \Pimple();
            foreach ($app['dbs.options'] as $name => $options) {
                $configs[$name] = new Configuration();

                if (isset($app['logger']) && class_exists('Intaro\PinbaBundle\Logger\DbalLogger')) {
                    $configs[$name]->setSQLLogger($app['doctrine.dbal.logger']);
                }
            }

            return $configs;
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     * @param Application $app
     */
    public function boot(Application $app) {
        if(!function_exists('pinba_script_name_set') || PHP_SAPI === 'cli')
        {
            return;
        }
        /**
         * @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface
         */
        $dispatcher = $app['dispatcher'];
        $dispatcher->addListener('kernel.request', array($app['intaro_pinba.script_name_configure.listener'], 'onRequest'));
        $app['twig'] = $app->extend('twig', function(\Twig_Environment $twig) use ($app) {
            /**
             * @see \Intaro\PinbaBundle\Twig\TimedTwigEngine
             */
            $className = $app['intaro_pinba.templating.engine.twig.class'];
            return new $className($twig, $app['intaro_pinba.stopwatch']);
        });

    }

}