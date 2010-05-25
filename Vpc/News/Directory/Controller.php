<?php
class Vpc_News_Directory_Controller extends Vpc_Directories_Item_Directory_Controller
{
    protected $_defaultOrder = array('field' => 'publish_date', 'direction' => 'DESC');

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('title', trlVps('Title'), 300));
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column_Date('publish_date', trlVps('Publish Date')));
        $this->_columns->add(new Vps_Grid_Column_Visible('visible'));
    }
}
