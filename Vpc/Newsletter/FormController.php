<?php
class Vpc_Newsletter_FormController extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save', 'add');
    protected $_formName = 'Vpc_Newsletter_Detail_Form';

    public function preDispatch()
    {
        parent::preDispatch();
        if ($this->_getParam('id')) {
            $this->_form->setId(
                $this->_getParam('componentId') . '_' . $this->_getParam('id')
            );
        }
    }

    public function jsonSaveAction()
    {
        if (!$this->_getParam('id')) {
            $row = $this->_form->getModel()->createRow();
            $row->component_id = $this->_getParam('componentId');
            $row->create_date = date('Y-m-d H:i:s');
            $row->save();
            $this->_form->setId($row->component_id . '_' . $row->id);
        }
        parent::jsonSaveAction();
    }
}
