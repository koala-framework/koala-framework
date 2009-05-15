<?php
class Vps_Data_Table extends Vps_Data_Abstract
{
    protected $_dataIndex;

    /**
     * @param string Spaltenname in Tabelle, standard ist Feldname
     **/
    public function __construct($dataIndex = null)
    {
        $this->_dataIndex = $dataIndex;
    }

    public function load($row)
    {
        $name = $this->getField();
        return $row->$name;
    }

    public function getField()
    {
        $name = $this->_dataIndex;
        if (!$name) $name = $this->getFieldname();
        return $name;
    }

    public function save(Vps_Model_Row_Interface $row, $data)
    {
        $name = $this->getField();
        $row->$name = $data;
    }
}
