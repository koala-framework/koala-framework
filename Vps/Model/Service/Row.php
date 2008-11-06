<?php
class Vps_Model_Service_Row extends Vps_Model_Row_Data_Abstract
{
    public function getCleanDataPrimary()
    {
        $pk = $this->_getPrimaryKey();
        if (isset($this->_cleanData) && isset($pk)) {
            return $this->_cleanData[$pk];
        }
        return null;
    }
}
