<?php
class Kwc_Paragraphs_Model extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_paragraphs';

    protected function _setupFilters()
    {
        $filter = new Kwf_Filter_Row_Numberize();
        $filter->setGroupBy('component_id');
        $this->_filters = array('pos' => $filter);
    }
}
