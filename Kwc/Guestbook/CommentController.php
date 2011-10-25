<?php
class Kwc_Guestbook_CommentController extends Kwf_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save');

    public function _initFields()
    {
        $this->_form->setModel(Kwc_Abstract::createChildModel($this->_getParam('class')));

        $this->_form->add(new Kwf_Form_Field_ShowField('create_time', trlKwf('Created')));
        $this->_form->add(new Kwf_Form_Field_Checkbox('visible', trlKwf('Visible')));
        $this->_form->add(new Kwf_Form_Field_TextField('name', trlKwf('Name')))
            ->setWidth(300);
        $this->_form->add(new Kwf_Form_Field_TextField('email', trlKwf('E-Mail')))
            ->setWidth(300);
        $this->_form->add(new Kwf_Form_Field_TextArea('content', trlKwf('Content')))
            ->setWidth(300)
            ->setHeight(160);
    }

    public function _beforeSave($row)
    {
    }
}
