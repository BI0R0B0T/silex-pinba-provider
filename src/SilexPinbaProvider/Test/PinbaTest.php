<?php
/**
 * @author Dolgov_M <mdol@1c.ru>
 * @date   15.11.2017 at 16:41
 */

namespace SilexPinbaProvider\Test;

use Intaro\PinbaBundle\Stopwatch\Stopwatch;
use PHPUnit\Framework\TestCase;
use Silex\Provider\TwigServiceProvider;
use SilexPinbaProvider\SilexPinbaProvider;
use Silex\Application;

class PinbaTest extends TestCase
{

    public function testTwigExtension()
    {


        $storage = new \ArrayObject();

        $app      = new ApplicationEmulator();
        $app
            ->register(new TwigServiceProvider(),array(
                'twig.templates' => array('hello' => 'Hello {{ name }}!'),
            ))
            ->register(new SilexPinbaProvider());

        $app['intaro_pinba.stopwatch.class'] = 'SilexPinbaProvider\Test\StopwatchEmulate';
        $app->boot();
        $app['intaro_pinba.stopwatch'] -> setStorage($storage);
        $app->renderView('hello');
        $this->assertTrue(is_array($storage['tags']), var_export($storage, true));
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