<?php
class Vps_Model_Field_Row extends Vps_Model_Row_Data_Abstract
{
    protected $_fieldName;
    protected $_siblingRow;

    public function __construct($config)
    {
        $this->_fieldName = $config['model']->getFieldName();
        $this->_siblingRow = $config['siblingRow'];
        parent::__construct($config);
    }

    public function getSiblingRow()
    {
        return $this->_siblingRow;
    }

    public function save()
    {
        Vps_Model_Row_Abstract::save();
        $this->_siblingRow->{$this->_fieldName} = serialize($this->_data);
    }

    public function delete()
    {
        throw new Vps_Exception("Can't delete sibling row");
    }
}
