<?php
class Vpc_Forum_Directory_Model extends Vps_Model_Db
{
    protected $_table = 'vpc_forum_groups';
    protected $_rowClass = 'Vpc_Forum_Directory_Row';
    protected $_toStringField = 'name';

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_AutoFill('{component_id}_{id}');
        $this->_filters = array('cache_child_component_id' => $filter);
    }
}
