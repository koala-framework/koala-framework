<?php
class Vpc_Advanced_Amazon_Nodes_Model extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_amazon_nodes';
    protected $_toStringField = 'name';

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_Numberize();
        $filter->setGroupBy('component_id');
        $this->_filters = array('pos' => $filter);
    }
}
