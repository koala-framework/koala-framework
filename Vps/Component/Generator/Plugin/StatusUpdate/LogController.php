<?php
class Vps_Component_Generator_Plugin_StatusUpdate_LogController extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_modelName = 'Vps_Component_Generator_Plugin_StatusUpdate_LogModel';
    protected $_buttons = array();

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column_Date('date', trlVps('Date')));
        $this->_columns->add(new Vps_Grid_Column('type', trlVps('Type')));
        $this->_columns->add(new Vps_Grid_Column('message', trlVps('Message'), 300));
    }
}
