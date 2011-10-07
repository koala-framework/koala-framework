<?php
/**
 * @deprecated use Vps_Util_Model_Pool instead
 */
class Vps_Dao_Pool extends Vps_Db_Table
{
    protected $_name = 'vps_pools';
    protected $_rowClass = 'Vps_Dao_Row_Pool';

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_Numberize();
        $filter->setGroupBy('pool');
        $this->_filters = array('pos' => $filter);
    }

    public function fetchPool($poolname, $order = 'pos')
    {
        $return = array();
        foreach ($this->fetchAll(array('pool = ?' => $poolname), $order) as $row) {
            $return[$row->value] = $row->value;
        }
        return $return;
    }
}
