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
        $value = null;
        if ($this->_trlRow) {
            $value = $this->_trlRow->$name;
        }
        if (!$value) {
            $value = parent::__get($name);
        }
        return $value;
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
