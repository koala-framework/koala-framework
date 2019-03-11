<?php
class Kwc_Events_Directory_Controller extends Kwc_News_Directory_Controller
{
    protected $_defaultOrder = array('field' => 'start_date', 'direction' => 'DESC');
    protected function _initColumns()
    {
        parent::_initColumns();
        unset($this->_columns['publish_date']);
        $this->_columns->insertAfter('title', new Kwf_Grid_Column('place', trlKwf('Place')));
        $this->_columns->insertAfter('title', new Kwf_Grid_Column_Date('end_date', trlKwf('End Date')));
        $this->_columns->insertAfter('title', new Kwf_Grid_Column_Date('start_date', trlKwf('Start Date')));
    }
}
