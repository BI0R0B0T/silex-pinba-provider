<?php
/**
 * @author Dolgov_M <mdol@1c.ru>
 * @date   15.11.2017 at 16:41
 */

namespace SilexPinbaProvider\Test;

use Intaro\PinbaBundle\Stopwatch\Stopwatch;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Silex\Provider\TwigServiceProvider;
use SilexPinbaProvider\SilexPinbaProvider;
use Silex\Application;

class PinbaTest extends TestCase
{

    public function testTwigExtension()
    {

        global $app;
        $storage = new \ArrayObject();
        $app     = new ApplicationEmulator();
        $emulate = false;
        if(!function_exists('pinba_script_name_set')) {
            $emulate = true;
            $app['pinba_logger'] = function () {
                return new PinbaLogger();
            };
            require __DIR__.'/../pinba_emulator.php';
        }
        $app
            ->register(new TwigServiceProvider(),array(
                'twig.templates' => array('hello' => 'Hello {{ name }}!'),
            ))
            ->register(new SilexPinbaProvider());

        $app['intaro_pinba.stopwatch.class'] = 'SilexPinbaProvider\Test\StopwatchEmulate';
        $app->boot();
        /**
         * @var $stopwatch StopwatchEmulate
         */
        $stopwatch = $app['intaro_pinba.stopwatch'];
        $this->assertTrue($stopwatch instanceof Stopwatch);
        $stopwatch-> setStorage($storage);
        $app->renderView('hello');
        $this->assertTrue(is_array($storage['tags']), var_export($storage, true));
        if($emulate) {
            /**
             * @var $logger PinbaLogger
             */
            $logger = $app['pinba_logger'];
            $this->assertTrue($logger instanceof PinbaLogger);
            $stack = $logger->getLogStack();
            $this->assertTrue(is_array($stack));
            $this->assertNotEmpty($stack);
            $expected = [
                ['debug', 'pinba_get_info', []],
            ];
            $this->assertEquals($expected, $stack);
        }
    }
}


class StopwatchEmulate extends Stopwatch
{

    /**
     * @var \ArrayObject
     */
    private $storage;

    /**
     * @param \ArrayObject $storage
     * @return $this
     */
    public function setStorage($storage)
    {
        $this->storage = $storage;
        return $this;
    }



    public function start(array $tags)
    {
        return new StopwatchEventEmulate($tags, $this->storage);
    }

}

class StopwatchEventEmulate
{
    /**
     * @var array
     */
    private $tags;
    /**
     * @var \ArrayObject
     */
    private $storage;

    /**
     * StopwatchEventEmulate constructor.
     * @param array $tags
     * @param \ArrayObject $storage
     */
    public function __construct(array $tags, \ArrayObject $storage)
    {
        $this->tags    = $tags;
        $this->storage = $storage;
    }


    public function stop()
    {
       $this->storage['tags'] = $this->tags;
    }

}


class ApplicationEmulator extends Application
{
    use Application\TwigTrait;
}

class PinbaLogger extends AbstractLogger {

    private $logStack = [];

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        $this->logStack[] = [$level, $message, $context];
    }

    /**
     * @return array
     */
    public function getLogStack()
    {
        return $this->logStack;
    }

}