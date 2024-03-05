<?php
class Kwf_Benchmark_Counter_Memcached
{
    private $_memcached;
    private $_prefix;
    public function __construct($config = array())
    {
        if (isset($config['prefix'])) {
            $this->_prefix = $config['prefix'];
        } else {
            $this->_prefix = Zend_Registry::get('config')->application->id.'-'.Kwf_Setup::getConfigSection().'-bench-';
        }
        if (isset($config['memcached'])) {
            $this->_memcached = $config['memcached'];
        }
    }

    public function getMemcache()
    {
        if ($this->_memcached) return $this->_memcached;

        static $memcached;
        if (!isset($memcached)) {
            $memcache = new Memcached;
            $memcacheSettings = Kwf_Registry::get('config')->server->memcache;

            $memcache->addServer($memcacheSettings->host, $memcacheSettings->port);
        }
        return $memcache;
    }

    public function increment($name, $value=1)
    {
        $memcache = $this->getMemcache();
        try {
            if (!($ret = $memcache->increment($this->_prefix.$name, $value))) {
                $ret = $memcache->set($this->_prefix.$name, $value);
            }
        } catch (ErrorException $e) {
            if ($e->getSeverity() == E_NOTICE) {
                $e = new Kwf_Exception_Other($e);
                $e->logOrThrow();
                return false;
            }
            throw $e;
        }
        return $ret;
    }

    public function getValue($name)
    {
        $memcache = $this->getMemcache();
        try {
            return $memcache->get($this->_prefix.$name);
        } catch (ErrorException $e) {
            if ($e->getSeverity() == E_NOTICE) {
                $e = new Kwf_Exception_Other($e);
                $e->logOrThrow();
                return false;
            }
            throw $e;
        }
    }

}
