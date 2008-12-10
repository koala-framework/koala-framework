<?php
class Vps_Form_Cards_TopModelRow extends Vps_Model_Row_Data_Abstract
{
    protected function _getSiblingRows()
    {
        $rows = parent::_getSiblingRows();
        return array($this->type => $rows[$this->type]);
    }

    public function getSilblingRow()
    {
        $rows = parent::_getSiblingRows();
        return $rows[$this->type];
    }
}