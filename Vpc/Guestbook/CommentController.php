<?php
class Vpc_Guestbook_CommentController extends Vpc_Guestbook_FormController
{
    protected $_buttons = array();
    protected $_permissions = array('save');

    public function _initFields()
    {
        $this->_form->setModel(Vpc_Abstract::createChildModel($this->_getParam('class')));

        $this->_form->add(new Vps_Form_Field_ShowField('create_time', trlVps('Created')));
        $this->_form->add(new Vps_Form_Field_Checkbox('visible', trlVps('Visible')));
        $this->_form->add(new Vps_Form_Field_TextField('name', trlVps('Name')))
            ->setWidth(300);
        $this->_form->add(new Vps_Form_Field_TextField('email', trlVps('E-Mail')))
            ->setWidth(300);
        $this->_form->add(new Vps_Form_Field_TextArea('content', trlVps('Content')))
            ->setWidth(300)
            ->setHeight(160);
    }

    public function _beforeSave($row)
    {
    }
}
