<?php
/**
 * @deprecated use Kwf_Util_Model_Pool instead
 */
class Kwf_Dao_Pool extends Kwf_Db_Table
{
    protected $_name = 'kwf_pools';
    protected $_rowClass = 'Kwf_Dao_Row_Pool';

    protected function _setupFilters()
    {
        $filter = new Kwf_Filter_Row_Numberize();
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
