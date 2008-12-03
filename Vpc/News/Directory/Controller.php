<?php
class Vpc_News_Directory_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array(
        'save' => true,
        'delete' => true,
        'reload' => true,
        'add'   => true
    );
    protected $_defaultOrder = array('field' => 'publish_date', 'direction' => 'DESC');
    //protected $_editDialog = array();

    protected function _initColumns()
    {
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
