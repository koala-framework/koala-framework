<?php
/**
 * @package Model
 * @internal
 */
class Kwf_Model_RowCache_Row extends Kwf_Model_Proxy_Row
{
    private $_cacheData = array();

    public function __construct(array $config)
    {
        if (isset($config['cacheData'])) $this->_cacheData = $config['cacheData'];
        if (!isset($config['row'])) $config['row'] = null;
        parent::__construct($config);
    }

    public function __get($name)
    {
        if (!$this->_row && array_key_exists($name, $this->_cacheData)) {
            return $this->_cacheData[$name];
        } else {
            $this->_loadRow();
            return parent::__get($name);
        }
    }

    public function __set($name, $value)
    {
        $this->_loadRow();
        parent::__set($name, $value);
    }

    public function toArray()
    {
        $this->_loadRow();
        return parent::toArray();
    }

    protected function _saveWithoutResetDirty()
    {
        $this->_loadRow();
        return parent::_saveWithoutResetDirty();
    }

    public function __isset($name)
    {
        if (!$this->_row && array_key_exists($name, $this->_cacheData)) {
            return true;
        } else {
            $this->_loadRow();
            return parent::__isset($name);
        }
    }

    public function delete()
    {
        $this->_loadRow();
        return parent::delete();
    }

    public function getProxiedRow()
    {
        $this->_loadRow();
        return parent::getProxiedRow();
    }

    private function _loadRow()
    {
        if (!$this->_row) {
            $id = $this->_cacheData[$this->_model->getPrimaryKey()];
            $this->_row = $this->_model->getSourceRowByIdForRow($id);
            if (!$this->_row) throw new Kwf_Exception("can't get row '$id'");
        }
        return $this->_row;
    }
}
