<?php
class Vps_Util_Rrd_File
{
    private $_fields = array();
    private $_fileName;
    public function __construct($fileName)
    {
        $this->_fileName = $fileName;
    }

    public function getFileName()
    {
        return $this->_fileName;
    }

    public function addField($f)
    {
        if (!($f instanceof Vps_Util_Rrd_Field)) {
            $f = new Vps_Util_Rrd_Field($f);
        }
        $this->_fields[] = $f;
    }

    public function getField($name)
    {
        foreach ($this->_fields as $f) {
            if ($f->nameEquals($name)) return $f;
        }
        return null;
    }

    public function createFile($start)
    {
        if (file_exists($this->_fileName)) {
            throw new Vps_Exception("$this->_fileName already exists");
        }
        $interval = 60;
        $cmd = "rrdtool create $this->_fileName ";
        $cmd .= "--start ".$start." ";
        $cmd .= "--step ".($interval)." ";
        foreach ($this->_fields as $field) {
            $cmd .= "DS:".$field->getName().":".$field->getType().":".($interval*2).":".$field->getMin().":".($field->getMax())." ";
        }
        $cmd .= "RRA:AVERAGE:0.6:1:2016 "; //1 woche
        $cmd .= "RRA:AVERAGE:0.6:7:1500 "; //1 Monat
        $cmd .= "RRA:AVERAGE:0.6:50:2500 "; //1 Jahr

        system($cmd, $ret);
        if ($ret != 0) throw new Vps_Exception("Command failed");
    }

    protected function _getMemcache()
    {
        if (!isset($this->_memcache)) {
            $this->_memcache = new Memcache;
            $memcacheSettings = Vps_Registry::get('config')->server->memcache;
            $this->_memcache->addServer($memcacheSettings->host, $memcacheSettings->port);
        }
        return $this->_memcache;
    }

    protected function _getMemcacheValue($field)
    {
        $prefix = Zend_Registry::get('config')->application->id.'-'.
                            Vps_Setup::getConfigSection().'-bench-';
        $value = $this->_getMemcache()->get($prefix.$field);
        if ($value===false) $value = 'U';
        return $value;
    }

    public function record(array $values)
    {
        if (!file_exists($this->_fileName)) {
            $this->createFile(time()-1);
        }

        $cmd = "rrdtool update $this->_fileName ";
        $cmd .= "N:";
        $cmd .= implode(':', $values);

        $ret = null;
        system($cmd, $ret);
        if ($ret != 0) throw new Vps_Exception("Command failed");
    }
}
