<?php
class Vpc_News_FormController extends Vps_Controller_Action_Auto_Vpc_Form
{
    protected $_buttons = array();

    public function _initFields()
    {
        $this->_form->add(new Vps_Auto_Field_TextField('title', 'Title'));
        $this->_form->add(new Vps_Auto_Field_TextArea('teaser', 'Teaser'))
            ->setWidth(300)
            ->setHeight(100);
        $this->_form->add(new Vps_Auto_Field_DateField('publish_date', 'Publish Date'));
        $this->_form->add(new Vps_Auto_Field_DateField('expiry_date', 'Expiry Date'));
    }
}
