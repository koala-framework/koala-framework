<?php
class Vps_Dao_Pages extends Vps_Db_Table_Abstract
{
    protected $_name = 'vps_pages';
    protected $_rowClass = 'Vps_Dao_Row_Page';
    protected $_referenceMap = array(
        'Parent'  => array('columns' => 'parent_id',
                         'refTableClass' => 'Vps_Dao_Pages',
                         'refColumns' => 'id'));

    protected function _setupFilters()
    {
        parent::_setupFilters();
        $this->_filters['filename'] = new Vps_Filter_Row_UniqueAscii();
        $this->_filters['filename']->setGroupBy(array('parent_id', 'domain'));
        $this->_filters['pos'] = new Vps_Filter_Row_Numberize();
        $this->_filters['pos']->setGroupBy(array('parent_id', 'category', 'domain'));
    }
}
