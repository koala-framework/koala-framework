<?php
class Vpc_Paragraphs_Model extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_paragraphs';

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_Numberize();
        $filter->setGroupBy('component_id');
        $this->_filters = array('pos' => $filter);
    }
}
