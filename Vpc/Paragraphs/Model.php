<?php
class Vpc_Paragraphs_Model extends Vpc_Table
{
    protected $_name = 'vpc_paragraphs';
    protected $_rowClass = 'Vpc_Paragraphs_Row';

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_Numberize();
        $filter->setGroupBy('component_id');
        $this->_filters = array('pos' => $filter);
    }
}
