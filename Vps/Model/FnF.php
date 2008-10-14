<?php
class Vps_Model_FnF extends Vps_Model_Data_Abstract
{
    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        if (!is_object($where)) {
            $select = $this->select($where, $order, $limit, $start);
        } else {
            $select = $where;
        }
        $data = $this->_selectData($select, $this->_data);
        return new $this->_rowsetClass(array(
            'model' => $this,
            'rowClass' => $this->_rowClass,
            'data' => $data
        ));
    }

    public function isEqual(Vps_Model_Interface $other)
    {
        return false;
    }
}
