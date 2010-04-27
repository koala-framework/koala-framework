<?php
class Vpc_Directories_Item_Directory_Trl_AdminModelRow extends Vps_Model_Proxy_Row
{
    protected $_trlRow;
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->_trlRow = $config['trlRow'];
    }

    public function __get($name)
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
        $this->_trlRow->__set($name, $value);
    }

    public function save()
    {
        return $this->_trlRow->save();
    }

    public function delete()
    {
        throw new Vps_Exception("Not possible");
    }
}
