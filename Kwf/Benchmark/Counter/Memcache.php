<?php
class Kwf_Benchmark_Counter_Memcache
{
    private $_memcache;
    private $_prefix;
    public function __construct($config = array())
    {
        if (isset($config['prefix'])) {
            $this->_prefix = $config['prefix'];
        } else {
            $this->_prefix = Zend_Registry::get('config')->application->id.'-'.Kwf_Setup::getConfigSection().'-bench-';
        }
        if (isset($config['memcache'])) {
            $this->_memcache = $config['memcache'];
        }
    }

    public function getMemcache()
    {
        if ($this->_memcache) return $this->_memcache;

        static $memcache;
        if (!isset($memcache)) {
            $memcache = new Memcache;
            $memcacheSettings = Kwf_Registry::get('config')->server->memcache;

            if (version_compare(phpversion('memcache'), '2.1.0') == -1 || phpversion('memcache')=='2.2.4') { // < 2.1.0
                $memcache->addServer($memcacheSettings->host, $memcacheSettings->port, true, 1, 1, 1);
            } else if (version_compare(phpversion('memcache'), '3.0.0') == -1) { // < 3.0.0
                $memcache->addServer($memcacheSettings->host, $memcacheSettings->port, true, 1, 1, 1, true, null, 10000);
            } else {
                $memcache->addServer($memcacheSettings->host, $memcacheSettings->port, true, 1, 1, 1);
            }
        }
        return $memcache;
    }

    public function increment($name, $value=1)
    {
        $memcache = $this->getMemcache();
        try {
            if (!($ret = $memcache->increment($this->_prefix.$name, $value))) {
                $ret = $memcache->set($this->_prefix.$name, $value, 0, 0);
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
