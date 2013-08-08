<?php
class Kwf_Cache_Backend_Yac extends Zend_Cache_Backend
{
    protected $_options = array(
        'cache_id_prefix' => '' //diese option ist normalerweise im frontend, aber hier auch nochmals im backend implenetiert
                                //damit kann nur ein prefix gecleant werden
    );
    protected $_yac;

    public function __construct(array $options = array())
    {
        if (!extension_loaded('yac')) {
            Zend_Cache::throwException('The yac extension must be loaded for using this backend !');
        }
        $this->_yac = new Yac();
        parent::__construct($options);
    }

    public function load($id, $doNotTestCacheValidity = false)
    {
        $id = $this->_processId($id);
        $tmp = $this->_yac->get($id);
        if (is_array($tmp)) {
            return $tmp[0];
        }
        return false;
    }

    public function loadWithMetadata($id, $doNotTestCacheValidity = false)
    {
        $id = $this->_processId($id);
        $tmp = $this->_yac->get($id);
        if (is_array($tmp)) {
            return array(
                'contents' => $tmp[0],
                'expire' => $tmp[2] ? $tmp[1] + $tmp[2] : null, //mtime + lifetime
            );
        }
        return false;
    }

    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        $id = $this->_processId($id);
        $lifetime = $this->getLifetime($specificLifetime);
        return $this->_yac->set($id, array($data, time(), $lifetime), $lifetime);
    }

    public function remove($id)
    {
        $id = $this->_processId($id);
        return $this->_yac->delete($id);
    }

    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
        switch ($mode) {
            case Zend_Cache::CLEANING_MODE_ALL:
                $this->_yac->flush();
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

    private function _processId($id)
    {
        static $cacheIdPrefix;
        if (!isset($cacheIdPrefix)) $cacheIdPrefix = Kwf_Cache::getUniquePrefix();
        $id = $cacheIdPrefix.$this->_options['cache_id_prefix'].$id;
        if (strlen($id) > Yac::YAC_MAX_KEY_LEN) {
            $id = md5($id);
        }
        return $id;
    }
}
