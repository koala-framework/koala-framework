<?php
class Vps_Model_FnF extends Vps_Model_Data_Abstract
{
    protected $_uniqueIdentifier;

    public function __construct(array $config = array())
    {
        if (isset($config['uniqueIdentifier'])) $this->_uniqueIdentifier = $config['uniqueIdentifier'];
        if (!isset($config['columns']) && isset($config['data'][0])) {
            $config['columns'] = array_keys($config['data'][0]);
        }
        parent::__construct($config);
    }

    public function setData(array $data)
    {
        $this->_data = $data;
        $this->_rows = array();
    }

    public function isEqual(Vps_Model_Interface $other)
    {
        return $this === $other;
    }

    public function getUniqueIdentifier()
    {
        if (isset($this->_uniqueIdentifier)) {
            return $this->_uniqueIdentifier;
        } else {
            throw new Vps_Exception("no uniqueIdentifier set");
        }
    }
}
