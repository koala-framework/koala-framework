<?php
class Vpc_Forum_FormController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save', 'add');
    protected $_tableName = 'Vpc_Forum_Group_Model';

    public function _initFields()
    {
        $this->_form->add(new Vps_Auto_Field_TextField('name', 'Group'))
            ->setAllowBlank(false)
            ->setWidth(300);
        $this->_form->add(new Vps_Auto_Field_TextArea('description', 'Description'))
            ->setWidth(300);

        $this->_form->add(new Vps_Auto_Field_Checkbox('post', 'enable posts'));
    }

    protected function _beforeInsert($row)
    {
        $row->parent_id = $this->_getParam('parent_id');
        $row->component_id = $this->_getParam('component_id');
        $row->pos = 0;
        $row->visible = 0;
    }
}
