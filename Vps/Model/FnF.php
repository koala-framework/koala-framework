<?php
class Vps_Model_FnF extends Vps_Model_Data_Abstract
{
    protected $_uniqueIdentifier;

    public function __construct(array $config = array())
    {
        if (isset($config['uniqueIdentifier'])) $this->_uniqueIdentifier = $config['uniqueIdentifier'];
        parent::__construct($config);
    }

    public function setData(array $data)
    {
        $this->_data = $data;
        $this->_rows = array();
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        if (!is_object($where)) {
            $select = $this->select($where, $order, $limit, $start);
        } else {
            $select = $where;
        }
        $dataKeys = $this->_selectDataKeys($select, $this->_data);
        return new $this->_rowsetClass(array(
            'model' => $this,
            'dataKeys' => $dataKeys
        ));
    }

    public function isEqual(Vps_Model_Interface $other)
    {
        return $this === $other;
    }

    public function getUniqueIdentifier() {
        if (isset($this->_uniqueIdentifier)) {
            return $this->_uniqueIdentifier;
        } else {
            throw new Vps_Exception("no uniqueIdentifier set");
        }
    }
}
