<?php
class Kwc_Basic_Table_Trl_AdminRow extends Kwf_Model_Proxy_Row
{
    protected $_trlRow;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->_trlRow = $config['trlRow'];
    }

    public function __get($name)
    {
        $ret = '';
        if ($this->_trlRow->hasColumn($name)) {
            $ret = $this->_trlRow->$name;
        }
        return $ret;
    }

    public function getFrontend($name)
    {
        $ret = null;
        if ($this->_trlRow->hasColumn($name)) {
            $ret = $this->_trlRow->$name;
        }
        if ($name != 'visible' && !$ret) {
            $ret = parent::__get($name);
        }
        return $ret;
    }

    public function __set($name, $value)
    {
        if ($name == 'visible') {
            $this->_trlRow->__set($name, $value);
        } else {
            $this->_trlRow->__set($name, $value);
        }
    }

    public function getMaster($name)
    {
        if (!$this->hasTrl($name)) {
            return parent::__get($name);
        } else {
            return '';
        }
    }

    public function hasTrl($name)
    {
        if ($this->_trlRow && $this->_trlRow->$name) {
            return true;
        } else {
            return false;
        }
    }

    protected function _saveWithoutResetDirty()
    {
        return $this->_trlRow->_saveWithoutResetDirty();
    }

    public function delete()
    {
        throw new Kwf_Exception("Not possible");
    }

    public function toArray()
    {
        $ret = parent::toArray();
        if ($this->_trlRow) {
            $ret = array_merge(
                parent::toArray(),
                $this->_trlRow->toArray()
            );
        }
        return $ret;
    }
}
