<?php
class Kwc_Events_Directory_Trl_Controller extends Kwc_Directories_Item_Directory_Trl_Controller
{
    protected $_defaultOrder = array('field' => 'start_date', 'direction' => 'DESC');

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column_Visible('visible'));
        $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title'), 300));
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column_Date('start_date', trlKwf('Publish Date')));
    }
}
