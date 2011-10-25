<?php
class Kwf_Util_Model_Pool extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwf_pools';
    protected $_rowClass = 'Kwf_Util_Model_Row_Pool';

    protected function _setupFilters()
    {
        $filter = new Kwf_Filter_Row_Numberize();
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
