<?php
class Vpc_Shop_Products_Directory_FormController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('add', 'save');
    protected $_tableName = 'Vpc_Shop_Products';
    public function _initFields()
    {
        $this->_form->add(new Vps_Form_Field_TextField('title', trlVps('Title')));
        $this->_form->add(new Vps_Form_Field_NumberField('price', trlVps('Price')));
        $this->_form->add(new Vps_Form_Field_Checkbox('visible', trlVps('Visible')));
        parent::_initFields();
    }
}
