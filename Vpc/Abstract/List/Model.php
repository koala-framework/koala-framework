<?php
class Vpc_Abstract_List_Model extends Vpc_Table
{
    protected $_name = 'vpc_composite_list';
    protected $_rowClass = 'Vpc_Abstract_List_Row';

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_Numberize();
        $filter->setGroupBy('component_id');
        $this->_filters = array('pos' => $filter);
    }
}
