<?php
class Vps_Util_Model_Pool extends Vps_Model_Db_Proxy
{
    protected $_table = 'vps_pools';
    protected $_rowClass = 'Vps_Util_Model_Row_Pool';

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_Numberize();
        $filter->setGroupBy('pool');
        $this->_filters = array('pos' => $filter);
    }

    public function fetchPool($poolname, $order = 'pos')
    {
        $return = array();
        $select = $this->select()->whereEquals('pool', $poolname)->order($order);
        foreach ($this->getRows($select) as $row) {
            $return[$row->value] = $row->value;
        }
        return $return;
    }
}
