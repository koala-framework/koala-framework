<?php
class Vps_Data_Date extends Vps_Data_Abstract
{
    protected $_dataIndex;
    protected $_showType;

    /**
     * @param string $dataIndex Spaltenname in Tabelle, standard ist Feldname
     * @param string $showType The output date type. Valid values: 'date' or 'datetime'
     **/
    public function __construct($dataIndex = null, $showType = 'date')
    {
        $this->_dataIndex = $dataIndex;
        switch ($showType) {
            case 'date': $this->_showType = trlVps('Y-m-d'); break;
            case 'datetime': $this->_showType = trlVps('Y-m-d H:i'); break;
            default: throw new Vps_Exception("Show type $showType not supported.");
        }
    }

    public function load($row)
    {
        $name = $this->_dataIndex;
        if (!$name) $name = $this->getFieldname();
        return date($this->_showType, strtotime($row->$name));
    }

    public function save(Vps_Model_Row_Interface $row, $data)
    {
        throw new Vps_Exception_NotYetImplemented();
    }
}
