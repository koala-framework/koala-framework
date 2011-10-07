<?php
class Vpc_Events_Directory_Controller extends Vpc_News_Directory_Controller
{
    protected $_defaultOrder = array('field' => 'start_date', 'direction' => 'DESC');
    protected function _initColumns()
    {
        parent::_initColumns();
        unset($this->_columns['publish_date']);
        $this->_columns->insertBefore('visible', new Vps_Grid_Column_Date('start_date', trlVps('Start Date')));
        $this->_columns->insertBefore('visible', new Vps_Grid_Column_Date('end_date', trlVps('End Date')));
        $this->_columns->insertBefore('start_date', new Vps_Grid_Column('place', trlVps('Place')));
    }
}
