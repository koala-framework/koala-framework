<?php
class Vpc_Posts_FormController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save' => true, 'add' => true);

    public function preDispatch()
    {
        $tablename = Vpc_Abstract::getSetting($this->class, 'tablename');
        $this->_table = new $tablename(array('componentClass'=>$this->class));
        parent::preDispatch();
    }

    public function _initFields()
    {
        $this->_form->add(new Vps_Auto_Field_TextField('name', trlVps('Name')))
            ->setWidth(300);
        $this->_form->add(new Vps_Auto_Field_TextField('email', trlVps('Email')))
            ->setWidth(300);
        $this->_form->add(new Vps_Auto_Field_TextArea('content', trlVps('Content')))
            ->setWidth(300)
            ->setHeight(150);
    }

    public function _beforeSave($row)
    {
        if ($this->_getParam('id') == 0 && $this->_getParam('component_id')) {
            $row->component_id = $this->_getParam('component_id');
            $row->visible = 0;
            $row->create_time = date('Y-m-d H:i:s');
        }
    }
}
