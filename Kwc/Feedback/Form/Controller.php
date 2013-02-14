<?php
class Kwc_Feedback_Form_Controller extends Kwf_Controller_Action_Auto_Grid
{
    protected $_model = 'Kwc_Feedback_Model';
    protected $_buttons = array();

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column_Datetime('date'));
        $this->_columns->add(new Kwf_Grid_Column('user_email', 'Benutzer', 150));
        $this->_columns->add(new Kwf_Grid_Column('text', 'Text', 500))
            ->setRenderer('nl2br');
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('component_id', $this->_getParam('componentId'));
        return $ret;
    }
}
