<?php
/**
 * @author Mikhail Dolgov <dolgov@bk.ru>
 * @date   09.03.2016 11:59
 */

namespace SilexPinbaProvider\Cache;

use Doctrine\Common\Cache\FilesystemCache;
use Intaro\PinbaBundle\Stopwatch\Stopwatch;
use Intaro\PinbaBundle\Stopwatch\StopwatchEvent;

class Filesystem extends FilesystemCache{
    /**
     * @var Stopwatch
     */
    protected $stopwatch;
    protected $stopwatchAdditionalTags = array();

    /**
     * @param Stopwatch $stopwatch
     */
    public function setStopwatch(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }

    /**
     * @param array $tags
     */
    public function setStopwatchTags(array $tags)
    {
        $this->stopwatchAdditionalTags = $tags;
    }

    /**
     * @param $methodName
     * @return StopwatchEvent
     */
    protected function getStopwatchEvent($methodName)
    {
        $tags = $this->stopwatchAdditionalTags;
        $tags['group'] = 'filesystem::' . $methodName;

        return $this->stopwatch->start($tags);
    }

    /**
     * Sets the namespace to prefix all cache ids with.
     *
     * @param string $namespace
     *
     * @return void
     */
    public function setNamespace($namespace) {
        if ($this->stopwatch) {
            $e = $this->getStopwatchEvent(__FUNCTION__);
        }
        parent::setNamespace($namespace);
        if ($this->stopwatch) {
            $e->stop();
        }
    }

    /**
     * Retrieves the namespace that prefixes all cache ids.
     *
     * @return string
     */
    public function getNamespace() {
        if ($this->stopwatch) {
            $e = $this->getStopwatchEvent(__FUNCTION__);
        }
        $result = parent::getNamespace();
        if ($this->stopwatch) {
            $e->stop();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($id) {
        if ($this->stopwatch) {
            $e = $this->getStopwatchEvent(__FUNCTION__);
        }
        $result = parent::fetch($id);
        if ($this->stopwatch) {
            $e->stop();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchMultiple(array $keys) {
        if ($this->stopwatch) {
            $e = $this->getStopwatchEvent(__FUNCTION__);
        }
        $result = parent::fetchMultiple($keys);
        if ($this->stopwatch) {
            $e->stop();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function contains($id) {
        if ($this->stopwatch) {
            $e = $this->getStopwatchEvent(__FUNCTION__);
        }
        $result = parent::contains($id);
        if ($this->stopwatch) {
            $e->stop();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function save($id, $data, $lifeTime = 0) {
        if ($this->stopwatch) {
            $e = $this->getStopwatchEvent(__FUNCTION__);
        }
        $result = parent::save($id, $data, $lifeTime);
        if ($this->stopwatch) {
            $e->stop();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id) {
        if ($this->stopwatch) {
            $e = $this->getStopwatchEvent(__FUNCTION__);
        }
        $result = parent::delete($id);
        if ($this->stopwatch) {
            $e->stop();
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getStats() {
        if ($this->stopwatch) {
            $e = $this->getStopwatchEvent(__FUNCTION__);
        }
        $result = parent::getStats();

    }

    /**
     * {@inheritDoc}
     */
    public function flushAll() {
        if ($this->stopwatch) {
            $e = $this->getStopwatchEvent(__FUNCTION__);
        }
        $result = parent::flushAll();
        if ($this->stopwatch) {
            $e->stop();
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAll() {
        if ($this->stopwatch) {
            $e = $this->getStopwatchEvent(__FUNCTION__);
        }
        $result = parent::deleteAll();
        if ($this->stopwatch) {
            $e->stop();
        }

        return $result;
    }

    /**
     * Gets the cache directory.
     *
     * @return string
     */
    public function getDirectory() {
        if ($this->stopwatch) {
            $e = $this->getStopwatchEvent(__FUNCTION__);
        }
        $result = parent::getDirectory();
        if ($this->stopwatch) {
            $e->stop();
        }

        return $result;
    }

    /**
     * Gets the cache file extension.
     *
     * @return string|null
     */
    public function getExtension() {
        if ($this->stopwatch) {
            $e = $this->getStopwatchEvent(__FUNCTION__);
        }
        $result = parent::getExtension();
        if ($this->stopwatch) {
            $e->stop();
        }

        return $result;
    }


}