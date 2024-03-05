<?php
class Kwf_Data_Values extends Kwf_Data_Abstract
{
    protected $_values;

    public function __construct($values)
    {
        $this->_values = $values;
    }

    public function load($row, array $info = array())
    {
        $name = $this->getFieldname();
        if (isset($this->_values[$row->$name])) {
            return $this->_values[$row->$name];
        }
        return null;
    }
}
