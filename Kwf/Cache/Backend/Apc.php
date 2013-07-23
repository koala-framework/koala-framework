<?php
class Kwf_Cache_Backend_Apc extends Zend_Cache_Backend_Apc
{
    protected $_options = array(
        'cache_id_prefix' => '' //diese option ist normalerweise im frontend, aber hier auch nochmals im backend implenetiert
                                //damit kann nur ein prefix gecleant werden
    );

    public function load($id, $doNotTestCacheValidity = false)
    {
        $id = $this->_processId($id);
        return parent::load($id, $doNotTestCacheValidity);
    }

    public function loadWithMetadata($id, $doNotTestCacheValidity = false)
    {
        $id = $this->_processId($id);

        $tmp = apc_fetch($id);
        if (is_array($tmp)) {
            return array(
                'contents' => $tmp[0],
                'expire' => $tmp[1] + $tmp[2], //mtime + lifetime
            );
        }
        return false;
    }

    public function test($id)
    {
        $id = $this->_processId($id);
        return parent::test($id);
    }

    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        $id = $this->_processId($id);
        return parent::save($data, $id, $tags, $specificLifetime);
    }

    public function remove($id)
    {
        $id = $this->_processId($id);
        $ret = true;
        if (php_sapi_name() == 'cli') {
            $ret = Kwf_Util_Apc::callClearCacheByCli(array('cacheIds' => $id));
        }
        return $ret && parent::remove($id);
    }

    public function getMetadatas($id)
    {
        $id = $this->_processId($id);
        return parent::getMetadatas($id);
    }

    public function touch($id, $extraLifetime)
    {
        $id = $this->_processId($id);
        return parent::touch($id, $extraLifetime);
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
                return $ret && apc_clear_cache('user');
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
        return $cacheIdPrefix.$this->_options['cache_id_prefix'].$id;
    }
}
