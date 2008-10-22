<?php
class Vps_Model_ProxyCache_Row extends Vps_Model_Proxy_Row
{
    protected $_cacheData;

    public function __construct(array $config)
    {
        if (isset($config['cacheData'])) $this->_cacheData = $config['cacheData'];
        if (!isset($config['row'])) $config['row'] = null;
        parent::__construct($config);
    }

    public function __get($name)
    {
        if (isset($this->_cacheData[$name])) {
            return $this->_cacheData[$name];
        } else {
            $this->_createRowIfNotExists();
            return parent::__get($name);
        }
    }

    private function _createRowIfNotExists() {
        if (!$this->_row) {
            $id = $this->_cacheData[$this->_model->getPrimaryKey()];
            $this->_row = $this->_model->getRowById($id);
        }
    }

    public function toArray()
    {
        if (isset($this->_row)) return $this->_row->toArray();
        else return $this->_cacheData;
    }

    public function save()
    {

        $this->_createRowIfNotExists();
        $ret = parent::save();
        $this->_model->clearCache();
        return $ret;
    }


    public function delete()
    {
        $this->_createRowIfNotExists();
        $ret = parent::delete();
        $this->_model->clearCache();
        return $ret;

    }
}
