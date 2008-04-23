<?php
abstract class Vps_Data_Abstract implements Vps_Data_Interface
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

    public function save(Vps_Model_Row_Interface $row, $data)
    {
        throw new Vps_Exception("Save is not possible for '".get_class($this)."'");
    }

    public function delete()
    {
        throw new Vps_Exception("Delete is not possible for '".get_class($this)."'");
    }
}
