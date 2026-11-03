<?php
/**
 * @author Mikhail Dolgov <dolgov@bk.ru>
 * @date   09.03.2016 15:28
 */

namespace SilexPinbaProvider\Twig;


use Intaro\PinbaBundle\Stopwatch\Stopwatch;
use Twig\Environment;

class TimedTwigEnvironment extends Environment
{

    /**
     * @var Stopwatch
     */
    private $stopwatch = null;

    /**
     * @var string
     */
    private $serverName = 'localhost';

    /**
     * @param Stopwatch $stopwatch
     * @return $this
     */
    public function setStopwatch($stopwatch)
    {
        $this->stopwatch = $stopwatch;
        return $this;
    }

    /**
     * @param string $serverName
     * @return $this
     */
    public function setServerName($serverName)
    {
        $this->serverName = $serverName;
        return $this;
    }

    /**
     * Renders a template.
     *
     * @param string $name The template name
     * @param array $context An array of parameters to pass to the template
     *
     * @return string The rendered template
     *
     * @throws \Twig\Error\LoaderError  When the template cannot be found
     * @throws \Twig\Error\SyntaxError  When an error occurred during compilation
     * @throws \Twig\Error\RuntimeError When an error occurred during rendering
     */
    public function render($name, array $context = array())
    {
        if(null === $this->stopwatch) {
            return parent::render($name, $context);
        }
        $e = $this->stopwatch->start(array(
            'server'        => $this->serverName,
            'group'         => 'twig::render',
            'twig_template' => (string)$name,
        ));

        $ret = parent::render($name, $context);
        $e->stop();
        return $ret;
    }
}