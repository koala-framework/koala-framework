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

    public static function fetchPool($poolname)
    {
        $return = array();
        $pool = Kwf_Model_Abstract::getInstance('Kwf_Util_Model_Pool');
        $select = $pool->select()->whereEquals('pool', $poolname)->order('pos');
        return $pool->getRows($select);
    }
}
