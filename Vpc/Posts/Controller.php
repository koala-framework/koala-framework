<?php
class Vpc_Posts_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array(
        'save' => true,
        'delete' => true,
        'reload' => true
    );
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_paging = 20;
    protected $_editDialog = array('controllerUrl'=>'/admin/component/edit/Vpc_Posts_Form',
                                   'width'=>500,
                                   'height'=>410);

    public function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('create_time', trlVps('Created'), 150));
        $this->_columns->add(new Vps_Grid_Column('content', trlVps('Content'), 350));
        $this->_columns->add(new Vps_Grid_Column_Visible());
    }
}
