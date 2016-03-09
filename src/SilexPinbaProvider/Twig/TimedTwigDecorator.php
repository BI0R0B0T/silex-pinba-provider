<?php
/**
 * @author Mikhail Dolgov <dolgov@bk.ru>
 * @date   09.03.2016 15:28
 */

namespace SilexPinbaProvider\Twig;


use Intaro\PinbaBundle\Stopwatch\Stopwatch;

class TimedTwigDecorator extends \Twig_Environment{
    /**
     * @var \Twig_Environment
     */
    private $environment;
    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * @param \Twig_Environment $environment
     * @param Stopwatch         $stopwatch
     */
    public function __construct(\Twig_Environment $environment, Stopwatch $stopwatch) {
        $this->environment = $environment;
        $this->stopwatch   = $stopwatch;
    }

    /**
     * Renders a template.
     *
     * @param string $name    The template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The rendered template
     *
     * @throws \Twig_Error_Loader  When the template cannot be found
     * @throws \Twig_Error_Syntax  When an error occurred during compilation
     * @throws \Twig_Error_Runtime When an error occurred during rendering
     */
    public function render($name, array $context = array()) {
        $e = $this->stopwatch->start(array(
            'server'        => 'localhost',
            'group'         => 'twig::render',
            'twig_template' => (string)$name,
        ));

        $ret = $this->environment->render($name, $context);

        $e->stop();

        return $ret;

    }

    /**
     * is triggered when invoking inaccessible methods in an object context.
     *
     * @param $name      string
     * @param $arguments array
     * @return mixed
     * @link http://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
     */
    function __call($name, $arguments) {
        return $this->environment->$name($arguments);
    }


}