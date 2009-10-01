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

    protected function _toArrayWithoutPrimaryKeys()
    {
        // TODO: wird in service model momentan nicht korrekt aufgerufen
        // theoretisch müssten dort auch die primarys von den siblings entfernt werden
        // aber der service hat momentan eh keine siblings.
        // test case wäre:
        return parent::_toArrayWithoutPrimaryKeys();
    }
}
