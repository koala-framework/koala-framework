<?php
abstract class Kwf_Data_Abstract implements Kwf_Data_Interface
{
    private $_fieldname;

    //wird autom. aufgerufen in Auto_Grid_Column::setData und Auto_Field::setData
    public function setFieldname($name)
    {
        $this->_fieldname = $name;
    }

    public function getFieldname()
    {
        return $this->_fieldname;
    }

    public function save(Kwf_Model_Row_Interface $row, $data)
    {
        throw new Kwf_Exception("Save is not possible for '".get_class($this)."'");
    }

    public function delete()
    {
        throw new Kwf_Exception("Delete is not possible for '".get_class($this)."'");
    }

    public function load($row, array $info = array())
    {
        throw new Kwf_Exception("Implement load '".get_class($this)."'");
    }
}
