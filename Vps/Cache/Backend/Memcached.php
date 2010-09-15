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
        foreach ($options['servers'] as &$s) {
            $s['retry_interval'] = 1;
            $s['timeoutms'] = 10000;
        }
        if (isset($options['filling_percentage_factor'])) {
            $this->_fillingPercentageFactor = $options['filling_percentage_factor'];
            if ($this->_fillingPercentageFactor > 1 || $this->_fillingPercentageFactor <= 0) {
                throw new Vps_Exception("Invalid filling_percentage_factor");
            }
            unset($options['filling_percentage_factor']);
        }

        if (!extension_loaded('memcache')) {
            Zend_Cache::throwException('The memcache extension must be loaded for using this backend !');
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
            if (!array_key_exists('persistent', $server)) {
                $server['persistent'] = self::DEFAULT_PERSISTENT;
            }
            if (!array_key_exists('weight', $server)) {
                $server['weight'] = self::DEFAULT_WEIGHT;
            }
            if (!array_key_exists('timeout', $server)) {
                $server['timeout'] = self::DEFAULT_TIMEOUT;
            }
            if (!array_key_exists('retry_interval', $server)) {
                $server['retry_interval'] = self::DEFAULT_RETRY_INTERVAL;
            }
            if (!array_key_exists('status', $server)) {
                $server['status'] = self::DEFAULT_STATUS;
            }
            if (!array_key_exists('failure_callback', $server)) {
                $server['failure_callback'] = self::DEFAULT_FAILURE_CALLBACK;
            }

            if (version_compare(phpversion('memcache'), '2.1.0') == -1) { // < 2.1.0
                $this->_memcache->addServer($server['host'], $server['port'], $server['persistent'],
                                        $server['weight'], $server['timeout'],
                                        $server['retry_interval']);
            } else if (version_compare(phpversion('memcache'), '3.0.0') == -1) { // < 3.0.0
                $this->_memcache->addServer($server['host'], $server['port'], $server['persistent'],
                                        $server['weight'], $server['timeout'],
                                        $server['retry_interval'],
                                        $server['status'], $server['failure_callback'],
                                        $server['timeoutms']);
            } else {
                $this->_memcache->addServer($server['host'], $server['port'], $server['persistent'],
                                        $server['weight'], $server['timeout'],
                                        $server['retry_interval'],
                                        $server['status'], $server['failure_callback']);
            }
        }


    }

    public function load($id, $doNotTestCacheValidity = false)
    {
        $id = $this->_processId($id);
        try {
            return parent::load($id, $doNotTestCacheValidity);
        } catch (ErrorException $e) {
            if ($e->getSeverity() == E_NOTICE) {
                $e = new Vps_Exception_Other($e);
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
                $e = new Vps_Exception_Other($e);
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
                $e = new Vps_Exception_Other($e);
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
                $e = new Vps_Exception_Other($e);
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
                $e = new Vps_Exception_Other($e);
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
                $e = new Vps_Exception_Other($e);
                $e->logOrThrow();
                return false;
            }
            throw $e;
        }
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
