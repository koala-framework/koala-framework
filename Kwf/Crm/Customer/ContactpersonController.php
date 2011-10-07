<?php
class Vps_Crm_Customer_ContactpersonController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add', 'delete');
    protected $_modelName = 'Vps_Crm_Customer_Model_Contactpersons';

    protected function _initFields()
    {
        parent::_initFields();
        $fs = $this->_form->add(new Vps_Form_Container_FieldSet(trlVps('Contact person')))
            ->setName('contactpersonfieldset');

        $fs->add(new Vps_Form_Field_Select('gender', trlVps('Gender')))
            ->setWidth(250)
            ->setValues(array(
                'male'   => trlVps('Mr.'),
                'female' => trlVps('Ms.')
            ));
        $fs->add(new Vps_Form_Field_TextField('title', trlVps('Title')))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_TextField('firstname', trlVps('Firstname')))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_TextField('lastname', trlVps('Lastname')))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_DateField('birthdate', trlVps('Date of birth')))
                ->setAllowBlank(true);
        $fs->add(new Vps_Form_Field_TextField('phone', trlVps('Phone')))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_TextField('mobile', trlVps('Mobile')))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_TextField('email', trlVps('E-Mail')))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_TextField('capacity', trlcVps('career', 'Capacity')))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_TextArea('annotation', trlVps('Annotation')))
            ->setWidth(250)
            ->setHeight(70);
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->customer_id = $this->_getParam('customer_id');
    }

}