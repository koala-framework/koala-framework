<?php
class Vpc_News_Directory_Trl_Controller extends Vpc_Directories_Item_Directory_Trl_Controller
{
    protected $_defaultOrder = array('field' => 'publish_date', 'direction' => 'DESC');

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column('title', trlVps('Title'), 300));
        $this->_columns->add(new Vps_Grid_Column_Button('properties', ' ', 20))
            ->setButtonIcon('/assets/silkicons/newspaper.png')
            ->setTooltip(trlVps('Properties'));
        $this->_columns->add(new Vps_Grid_Column_Button('edit', ' ', 20))
            ->setButtonIcon('/assets/silkicons/newspaper_go.png')
            ->setTooltip(trlVps('Edit News'));
        $this->_columns->add(new Vps_Grid_Column_Date('publish_date', trlVps('Publish Date')));
        $this->_columns->add(new Vps_Grid_Column_Visible('visible'));
    }
}
