<?php
class Kwf_Component_Generator_Plugin_StatusUpdate_LogController extends Kwf_Controller_Action_Auto_Kwc_Grid
{
    protected $_modelName = 'Kwf_Component_Generator_Plugin_StatusUpdate_LogModel';
    protected $_buttons = array();

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column_Date('date', trlKwf('Date')));
        $this->_columns->add(new Kwf_Grid_Column('type', trlKwf('Type')));
        $this->_columns->add(new Kwf_Grid_Column('message', trlKwf('Message'), 300));
    }
}
