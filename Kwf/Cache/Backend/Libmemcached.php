<?php
class Kwf_Cache_Backend_Libmemcached extends Zend_Cache_Backend_Libmemcached
{
    private $_fillingPercentageFactor = 1;

    public function __construct(array $options = array())
    {
        if (!isset($options['servers'])) {
            $options['servers'] = array(array(
                'host' => Kwf_Cache_Simple::$memcacheHost,
                'port' => Kwf_Cache_Simple::$memcachePort
            ));
        }
        if (isset($options['filling_percentage_factor'])) {
            $this->_fillingPercentageFactor = $options['filling_percentage_factor'];
            if ($this->_fillingPercentageFactor > 1 || $this->_fillingPercentageFactor <= 0) {
                throw new Kwf_Exception("Invalid filling_percentage_factor");
            }
            unset($options['filling_percentage_factor']);
        }

        if (!extension_loaded('memcached')) {
            Zend_Cache::throwException('The memcached extension must be loaded for using this backend !');
        }
        Zend_Cache_Backend::__construct($options);
        if (isset($this->_options['servers'])) {
            $value= $this->_options['servers'];
            if (isset($value['host'])) {
                // in this case, $value seems to be a simple associative array (one server only)
                $value = array(0 => $value); // let's transform it into a classical array of associative arrays
            }
            $this->setOption('servers', $value);
        }
        $this->_memcache = new Memcache;
        foreach ($this->_options['servers'] as $server) {
            if (!array_key_exists('port', $server)) {
                $server['port'] = self::DEFAULT_PORT;
            }
            if (!array_key_exists('weight', $server)) {
                $server['weight'] = self::DEFAULT_WEIGHT;
            }
            $this->_memcache->addServer($server['host'], $server['port']);
        }


    }

    public function load($id, $doNotTestCacheValidity = false)
    {
        $id = $this->_processId($id);
        try {
            return parent::load($id, $doNotTestCacheValidity);
        } catch (ErrorException $e) {
            if ($e->getSeverity() == E_NOTICE) {
                $e = new Kwf_Exception_Other($e);
                $e->logOrThrow();
                return false;
            }
            throw $e;
        }
    }

    public function loadWithMetadata($id, $doNotTestCacheValidity = false)
    {
        $id = $this->_processId($id);
        try {
            $tmp = $this->_memcache->get($id);
            if (is_array($tmp) && isset($tmp[0])) {
                return array(
                    'contents' => $tmp[0],
                    'expire' => $tmp[2] ? ($tmp[1] + $tmp[2]) : null, //mtime + lifetime
                );
            }
            return false;
        } catch (ErrorException $e) {
            if ($e->getSeverity() == E_NOTICE) {
                $e = new Kwf_Exception_Other($e);
                $e->logOrThrow();
                return false;
            }
            throw $e;
        }
    }

    public function test($id)
    {
        $id = $this->_processId($id);
        try {
            return parent::test($id);
        } catch (ErrorException $e) {
            if ($e->getSeverity() == E_NOTICE) {
                $e = new Kwf_Exception_Other($e);
                $e->logOrThrow();
                return false;
            }
            throw $e;
        }
    }

    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        $id = $this->_processId($id);
        try {
            return parent::save($data, $id, $tags, $specificLifetime);
        } catch (ErrorException $e) {
            if ($e->getSeverity() == E_NOTICE) {
                $e = new Kwf_Exception_Other($e);
                $e->logOrThrow();
                return false;
            }
            throw $e;
        }
    }

    public function remove($id)
    {
        $id = $this->_processId($id);
        try {
            return parent::remove($id);
        } catch (ErrorException $e) {
            if ($e->getSeverity() == E_NOTICE) {
                $e = new Kwf_Exception_Other($e);
                $e->logOrThrow();
                return false;
            }
            throw $e;
        }
    }

    public function getMetadatas($id)
    {
        $id = $this->_processId($id);
        try {
            return parent::getMetadatas($id);
        } catch (ErrorException $e) {
            if ($e->getSeverity() == E_NOTICE) {
                $e = new Kwf_Exception_Other($e);
                $e->logOrThrow();
                return false;
            }
            throw $e;
        }
    }

    public function touch($id, $extraLifetime)
    {
        $id = $this->_processId($id);
        try {
            return parent::touch($id, $extraLifetime);
        } catch (ErrorException $e) {
            if ($e->getSeverity() == E_NOTICE) {
                $e = new Kwf_Exception_Other($e);
                $e->logOrThrow();
                return false;
            }
            throw $e;
        }
    }

    protected function _getCacheIdPrefix()
    {
        static $cacheIdPrefix;
        if (!isset($cacheIdPrefix)) {
            $cacheIdPrefix = Kwf_Config::getValue('application.id');
            if (!$cacheIdPrefix) {
                throw new Kwf_Exception("application.id has to be set in config");
            }
            $cacheIdPrefix .= Kwf_Setup::getConfigSection();
        }
        return $cacheIdPrefix;
    }

    protected function _processId($id)
    {
        $id = $this->_getCacheIdPrefix().$id;
        return $id;
    }

    public function getFillingPercentage()
    {
        $ret = parent::getFillingPercentage();
        $ret *= $this->_fillingPercentageFactor;
        return $ret;
    }
}
