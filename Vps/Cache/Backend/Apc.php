<?php
class Vps_Cache_Backend_Apc extends Zend_Cache_Backend_Apc
{
    public function load($id, $doNotTestCacheValidity = false)
    {
        $id = $this->_processId($id);
        return parent::load($id, $doNotTestCacheValidity);
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
        return parent::remove($id);
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

    private function _processId($id)
    {
        static $cacheIdPrefix;
        if (!isset($cacheIdPrefix)) $cacheIdPrefix = Vps_Cache::getUniquePrefix();
        return $cacheIdPrefix.$id;
    }
}
