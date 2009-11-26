<?php
class Vps_Cache_Backend_Memcached extends Zend_Cache_Backend_Memcached
{
    private $_fillingPercentageFactor = 1;

    public function __construct(array $options = array())
    {
        if (!isset($options['servers'])) {
            $options['servers'] = array(array(
                'host' => Vps_Registry::get('config')->server->memcache->host,
                'port' => Vps_Registry::get('config')->server->memcache->port
            ));
        }
        $options['compatibility'] = true;
        if (isset($options['filling_percentage_factor'])) {
            $this->_fillingPercentageFactor = $options['filling_percentage_factor'];
            if ($this->_fillingPercentageFactor > 1 || $this->_fillingPercentageFactor <= 0) {
                throw new Vps_Exception("Invalid filling_percentage_factor");
            }
            unset($options['filling_percentage_factor']);
        }
        parent::__construct($options);
    }

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

    public function getFillingPercentage()
    {
        $ret = parent::getFillingPercentage();
        $ret *= $this->_fillingPercentageFactor;
        return $ret;
    }

}
