<?php
class Vps_Model_ProxyCache_Row extends Vps_Model_Proxy_Row
{
    protected $_cacheData = array();
    private $_id;

    public function __construct(array $config)
    {
        if (isset($config['cacheData'])) $this->_cacheData = $config['cacheData'];
        if (isset($config['id'])) $this->_id = $config['id'];
        if (!isset($config['row'])) $config['row'] = null;
        parent::__construct($config);
    }

    //drinnen gelassen -> vielleicht wird es später noch gebraucht
    public function getid() {
        return $this->_id;
    }
    public function __get($name)
    {
        if (array_key_exists($name, $this->_cacheData)) {
            return $this->_cacheData[$name];
        } else {
            $this->_getRow();
            return parent::__get($name);
        }
    }

    public function __set($name, $value)
    {
	    $this->_getRow();
	    parent::__set($name, $value);
    }

    public function toArray()
    {
        return parent::$this->_getRow()->toArray();
    }

    public function save()
    {
        $id = $this->{$this->_getPrimaryKey()};
        $this->_getRow();
        $ret = parent::save();
        if (!$id) {
            $this->_model->afterInsert($this);
        } else {
            $this->_model->afterUpdate($this);
        }
        $this->_model->clearCacheStore();
        return $ret;
    }

    public function __isset($name)
    {
        if (array_key_exists($name, $this->_cacheData)) {
            return $this->_cacheData[$name];
        } else {
            $this->_getRow();
            return parent::__isset($name);
        }
    }

    public function delete()
    {
        $this->_model->deleteCacheDataRow($this->_getRow());
        $ret = parent::delete();
        $this->_model->clearCacheStore();
        return $ret;
    }

    private function _getRow()
    {
        if (!$this->_row) {
            $id = $this->_cacheData[$this->_model->getPrimaryKey()];
            $this->_row = $this->_model->getRowById($id);
        }
        return $this->_row;
    }
}
