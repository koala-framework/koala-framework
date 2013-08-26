<?php
class Kwf_Cache_Backend_Apcu extends Zend_Cache_Backend implements Zend_Cache_Backend_ExtendedInterface
{
    protected $_options = array(
        'cache_id_prefix' => '' //diese option ist normalerweise im frontend, aber hier auch nochmals im backend implenetiert
                                //damit kann nur ein prefix gecleant werden
    );

    public function __construct(array $options = array())
    {
        if (!extension_loaded('apcu')) {
            Zend_Cache::throwException('The apcu extension must be loaded for using this backend !');
        }
        parent::__construct($options);
    }

    public function load($id, $doNotTestCacheValidity = false)
    {
        $id = $this->_processId($id);
        $tmp = apcu_fetch($id);
        if (is_array($tmp)) {
            return $tmp[0];
        }
        return false;
    }
    
    public function loadWithMetadata($id, $doNotTestCacheValidity = false)
    {
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
        $lifetime = $this->getLifetime($specificLifetime);
        $result = apcu_store($id, array($data, time(), $lifetime), $lifetime);
        return $result;
    }

    public function remove($id)
    {
        $id = $this->_processId($id);
        return apcu_delete($id);
    }

    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
        switch ($mode) {
            case Zend_Cache::CLEANING_MODE_ALL:
                return apcu_clear_cache();
                break;
            case Zend_Cache::CLEANING_MODE_OLD:
                break;
            case Zend_Cache::CLEANING_MODE_MATCHING_TAG:
            case Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG:
            case Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG:
                break;
            default:
                Zend_Cache::throwException('Invalid mode for clean() method');
                break;
        }
    }

    public function isAutomaticCleaningAvailable()
    {
        return false;
    }

    public function getFillingPercentage()
    {
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

    public function getTags()
    {
        return array();
    }

    public function getIdsMatchingTags($tags = array())
    {
        return array();
    }

    public function getIdsNotMatchingTags($tags = array())
    {
        return array();
    }

    public function getIdsMatchingAnyTags($tags = array())
    {
        return array();
    }

    public function getIds()
    {
        return array();
    }

    public function getMetadatas($id)
    {
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

    public function getCapabilities()
    {
        return array(
            'automatic_cleaning' => false,
            'tags' => false,
            'expired_read' => false,
            'priority' => false,
            'infinite_lifetime' => false,
            'get_list' => false
        );
    }

    private function _processId($id)
    {
        static $cacheIdPrefix;
        if (!isset($cacheIdPrefix)) $cacheIdPrefix = Kwf_Cache::getUniquePrefix();
        return $cacheIdPrefix.$this->_options['cache_id_prefix'].$id;
    }

}
