<?php
class Kwf_Data_Table extends Kwf_Data_Abstract
{
    protected $_dataIndex;

    /**
     * @param string Spaltenname in Tabelle, standard ist Feldname
     **/
    public function __construct($dataIndex = null)
    {
        $this->_dataIndex = $dataIndex;
    }

    public function load($row, array $info = array())
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

    public function save(Kwf_Model_Row_Interface $row, $data)
    {
        $name = $this->getField();
        $row->$name = $data;
    }
}
