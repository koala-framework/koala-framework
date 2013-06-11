<?php
class Kwc_Calendar_Directory_Controller extends Kwc_Directories_Item_Directory_Controller
{
    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('title', 'Titel'));
        $this->_columns->add(new Kwf_Grid_Column_Datetime('from', 'Start'));
        $this->_columns->add(new Kwf_Grid_Column_Datetime('to', 'Ende'));
        parent::_initColumns();
    }
}
