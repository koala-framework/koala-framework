<?php
class Kwf_Cache_Backend_Apcu extends Zend_Cache_Backend implements Zend_Cache_Backend_ExtendedInterface
{
    /**
     * Log message
     */
    const TAGS_UNSUPPORTED_BY_CLEAN_OF_APC_BACKEND = 'Zend_Cache_Backend_Apc::clean() : tags are unsupported by the Apcu backend';
    const TAGS_UNSUPPORTED_BY_SAVE_OF_APC_BACKEND =  'Zend_Cache_Backend_Apc::save() : tags are unsupported by the Apcu backend';

    public function __construct(array $options = array())
    {
        if (isset($options['cache_id_prefix'])) {
            throw new Kwf_Exception("Unsupported optoin for Apcu backend: cache_id_prefix");
        }
        if (!extension_loaded('apcu')) {
            Zend_Cache::throwException('The apcu extension must be loaded for using this backend !');
        }
        parent::__construct($options);
    }

    public function load($id, $doNotTestCacheValidity = false)
    {
        if (php_sapi_name() == 'cli') return false;

        $id = $this->_processId($id);

        $tmp = apcu_fetch($id);
        if (is_array($tmp)) {
            return $tmp[0];
        }
        return false;
    }

    public function loadWithMetadata($id, $doNotTestCacheValidity = false)
    {
        if (php_sapi_name() == 'cli') return false;

        $id = $this->_processId($id);

        $tmp = apcu_fetch($id);
        if (is_array($tmp)) {
            return array(
                'contents' => $tmp[0],
                'expire' => $tmp[2] ? $tmp[1] + $tmp[2] : null, //mtime + lifetime
            );
        }
        return false;
    }

    public function test($id)
    {
        if (php_sapi_name() == 'cli') return false;

        $id = $this->_processId($id);
        $tmp = apcu_fetch($id);
        if (is_array($tmp)) {
            return $tmp[1];
        }
        return false;
    }

    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        $id = $this->_processId($id);
        if (php_sapi_name() == 'cli') {
            $lifetime = $this->getLifetime($specificLifetime);
            Kwf_Util_Apc::callSaveCacheByCli(array('id' => $id, 'data' => serialize(array($data, time(), $lifetime))));
        }
        $lifetime = $this->getLifetime($specificLifetime);
        $result = apcu_store($id, array($data, time(), $lifetime), $lifetime);
        if (count($tags) > 0) {
            $this->_log(self::TAGS_UNSUPPORTED_BY_SAVE_OF_APC_BACKEND);
        }
        return true; //silently ignore apcu_store returning false
    }

    public function remove($id)
    {
        $id = $this->_processId($id);
        $ret = true;
        if (php_sapi_name() == 'cli') {
            $ret = Kwf_Util_Apc::callClearCacheByCli(array('cacheIds' => $id));
        }
        return $ret && apcu_delete($id);
    }

    public function getMetadatas($id)
    {
        if (php_sapi_name() == 'cli') return false;

        $id = $this->_processId($id);
        $tmp = apcu_fetch($id);
        if (is_array($tmp)) {
            $data = $tmp[0];
            $mtime = $tmp[1];
            if (!isset($tmp[2])) {
                // because this record is only with 1.7 release
                // if old cache records are still there...
                return false;
            }
            $lifetime = $tmp[2];
            return array(
                'expire' => $mtime + $lifetime,
                'tags' => array(),
                'mtime' => $mtime
            );
        }
        return false;
    }

    public function touch($id, $extraLifetime)
    {
        if (php_sapi_name() == 'cli') return false;

        $id = $this->_processId($id);
        $tmp = apcu_fetch($id);
        if (is_array($tmp)) {
            $data = $tmp[0];
            $mtime = $tmp[1];
            if (!isset($tmp[2])) {
                // because this record is only with 1.7 release
                // if old cache records are still there...
                return false;
            }
            $lifetime = $tmp[2];
            $newLifetime = $lifetime - (time() - $mtime) + $extraLifetime;
            if ($newLifetime <=0) {
                return false;
            }
            apcu_store($id, array($data, time(), $newLifetime), $newLifetime);
            return true;
        }
        return false;
    }

    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
        switch ($mode) {
            case Zend_Cache::CLEANING_MODE_ALL:
                $ret = true;
                if (php_sapi_name() == 'cli') {
                    $ret = Kwf_Util_Apc::callClearCacheByCli(array(
                        'type' => 'user'
                    ));
                }
                return $ret && apcu_clear_cache();
                break;
            case Zend_Cache::CLEANING_MODE_OLD:
                $this->_log("Zend_Cache_Backend_Apc::clean() : CLEANING_MODE_OLD is unsupported by the Apc backend");
                break;
            case Zend_Cache::CLEANING_MODE_MATCHING_TAG:
            case Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG:
            case Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG:
                $this->_log(self::TAGS_UNSUPPORTED_BY_CLEAN_OF_APC_BACKEND);
                break;
            default:
                Zend_Cache::throwException('Invalid mode for clean() method');
                break;
        }
    }

    private function _processId($id)
    {
        static $cacheIdPrefix;
        if (!isset($cacheIdPrefix)) $cacheIdPrefix = Kwf_Cache::getUniquePrefix();
        return $cacheIdPrefix.$id;
    }

    public function getFillingPercentage()
    {
        if (php_sapi_name() == 'cli') {
            return 0;
        }
        $mem = apcu_sma_info(true);
        $memSize    = $mem['num_seg'] * $mem['seg_size'];
        $memAvailable= $mem['avail_mem'];
        $memUsed = $memSize - $memAvailable;
        if ($memSize == 0) {
            Zend_Cache::throwException('can\'t get apc memory size');
        }
        if ($memUsed > $memSize) {
            return 100;
        }
        return ((int) (100. * ($memUsed / $memSize)));
    }

    /**
     * Return true if the automatic cleaning is available for the backend
     *
     * DEPRECATED : use getCapabilities() instead
     *
     * @deprecated
     * @return boolean
     */
    public function isAutomaticCleaningAvailable()
    {
        return false;
    }

    /**
     * Return an array of stored tags
     *
     * @return array array of stored tags (string)
     */
    public function getTags()
    {
        $this->_log(self::TAGS_UNSUPPORTED_BY_SAVE_OF_APC_BACKEND);
        return array();
    }

    /**
     * Return an array of stored cache ids which match given tags
     *
     * In case of multiple tags, a logical AND is made between tags
     *
     * @param array $tags array of tags
     * @return array array of matching cache ids (string)
     */
    public function getIdsMatchingTags($tags = array())
    {
        $this->_log(self::TAGS_UNSUPPORTED_BY_SAVE_OF_APC_BACKEND);
        return array();
    }

    /**
     * Return an array of stored cache ids which don't match given tags
     *
     * In case of multiple tags, a logical OR is made between tags
     *
     * @param array $tags array of tags
     * @return array array of not matching cache ids (string)
     */
    public function getIdsNotMatchingTags($tags = array())
    {
        $this->_log(self::TAGS_UNSUPPORTED_BY_SAVE_OF_APC_BACKEND);
        return array();
    }

    /**
     * Return an array of stored cache ids which match any given tags
     *
     * In case of multiple tags, a logical AND is made between tags
     *
     * @param array $tags array of tags
     * @return array array of any matching cache ids (string)
     */
    public function getIdsMatchingAnyTags($tags = array())
    {
        $this->_log(self::TAGS_UNSUPPORTED_BY_SAVE_OF_APC_BACKEND);
        return array();
    }

    /**
     * Return an array of stored cache ids
     *
     * @return array array of stored cache ids (string)
     */
    public function getIds()
    {
        $ids      = array();
        $iterator = new APCUIterator('user', null, APCU_ITER_KEY);
        foreach ($iterator as $item) {
            $ids[] = $item['key'];
        }

        return $ids;
    }

    /**
     * Return an associative array of capabilities (booleans) of the backend
     *
     * The array must include these keys :
     * - automatic_cleaning (is automating cleaning necessary)
     * - tags (are tags supported)
     * - expired_read (is it possible to read expired cache records
     *                 (for doNotTestCacheValidity option for example))
     * - priority does the backend deal with priority when saving
     * - infinite_lifetime (is infinite lifetime can work with this backend)
     * - get_list (is it possible to get the list of cache ids and the complete list of tags)
     *
     * @return array associative of with capabilities
     */
    public function getCapabilities()
    {
        return array(
            'automatic_cleaning' => false,
            'tags' => false,
            'expired_read' => false,
            'priority' => false,
            'infinite_lifetime' => false,
            'get_list' => true
        );
    }
}
