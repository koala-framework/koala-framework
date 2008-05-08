<?php
class Vpc_Formular_Dynamic_Model extends Vpc_Paragraphs_Model
{
    protected $_name = 'vpc_formular';

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_Numberize();
        $filter->setGroupBy('component_id', 'parent_id');
        $this->_filters = array('pos' => $filter);
    }
}
