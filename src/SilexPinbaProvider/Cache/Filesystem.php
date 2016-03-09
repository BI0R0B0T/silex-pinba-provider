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
     * @return StopwatchEvent|null
     */
    protected function getStopwatchEvent($methodName)
    {
        if (!$this->stopwatch) {
            return null;
        }
        $tags = $this->stopwatchAdditionalTags;
        $tags['group'] = 'filesystem::' . $methodName;

        return $this->stopwatch->start($tags);
    }


    /**
     * Fetches an entry from the cache.
     *
     * @param string $id The id of the cache entry to fetch.
     *
     * @return mixed|false The cached data or FALSE, if no cache entry exists for the given id.
     */
    protected function doFetch($id)
    {
        $e = $this->getStopwatchEvent(__FUNCTION__);
        $result = parent::doFetch($id);
        if ($e) {
            $e->stop();
        }

        return $result;
    }

    /**
     * Tests if an entry exists in the cache.
     *
     * @param string $id The cache id of the entry to check for.
     *
     * @return bool TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    protected function doContains($id)
    {
        $e = $this->getStopwatchEvent(__FUNCTION__);
        $result = parent::doContains($id);
        if ($e) {
            $e->stop();
        }
        return $result;
    }

    /**
     **
     * Puts data into the cache.
     *
     * @param string $id       The cache id.
     * @param string $data     The cache entry/data.
     * @param int    $lifeTime The lifetime. If != 0, sets a specific lifetime for this
     *                           cache entry (0 => infinite lifeTime).
     *
     * @return bool TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        $e = $this->getStopwatchEvent(__FUNCTION__);
        $result = parent::doSave($id, $data, $lifeTime);
        if ($e) {
            $e->stop();
        }
        return $result;
    }
}