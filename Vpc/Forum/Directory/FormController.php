<?php
class Vpc_Forum_Directory_FormController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Vpc_Forum_Directory_Model';

    public function _initFields()
    {
        $this->_form->add(new Vps_Form_Field_TextField('name', trlVps('Group')))
            ->setAllowBlank(false)
            ->setWidth(300);
        $this->_form->add(new Vps_Form_Field_TextArea('description', trlVps('Description')))
            ->setWidth(300);

        $this->_form->add(new Vps_Form_Field_Checkbox('post', trlVps('enable posts')));
    }

    protected function _beforeInsert($row)
    {
        if ($this->_getParam('parent_id')) {
            $row->parent_id = $this->_getParam('parent_id');
        }
        $row->component_id = $this->_getParam('componentId');
        $row->pos = 0;
        $row->visible = 0;
    }
}
