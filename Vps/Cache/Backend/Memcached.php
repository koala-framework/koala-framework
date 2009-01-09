<?php
class Vps_Cache_Backend_Memcached extends Zend_Cache_Backend_Memcached
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

    private function _processId($id)
    {
        static $cacheIdPrefix;
        if (!isset($cacheIdPrefix)) {
            $cacheIdPrefix = Zend_Registry::get('config')->application->id;
            if (!$cacheIdPrefix) {
                throw new Vps_Exception("application.id has to be set in config");
            }
            $cacheIdPrefix .= Vps_Setup::getConfigSection();
        }
        $id = md5($cacheIdPrefix.$id);
        return $id;
    }

}
