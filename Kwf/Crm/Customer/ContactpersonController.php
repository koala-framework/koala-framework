<?php
class Kwf_Crm_Customer_ContactpersonController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add', 'delete');
    protected $_modelName = 'Kwf_Crm_Customer_Model_Contactpersons';

    protected function _initFields()
    {
        parent::_initFields();
        $fs = $this->_form->add(new Kwf_Form_Container_FieldSet(trlKwf('Contact person')))
            ->setName('contactpersonfieldset');

        $fs->add(new Kwf_Form_Field_Select('gender', trlKwf('Gender')))
            ->setWidth(250)
            ->setValues(array(
                'male'   => trlKwf('Mr.'),
                'female' => trlKwf('Ms.')
            ));
        $fs->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
            ->setWidth(250);
        $fs->add(new Kwf_Form_Field_TextField('firstname', trlKwf('Firstname')))
            ->setWidth(250);
        $fs->add(new Kwf_Form_Field_TextField('lastname', trlKwf('Lastname')))
            ->setWidth(250);
        $fs->add(new Kwf_Form_Field_DateField('birthdate', trlKwf('Date of birth')))
                ->setAllowBlank(true);
        $fs->add(new Kwf_Form_Field_TextField('phone', trlKwf('Phone')))
            ->setWidth(250);
        $fs->add(new Kwf_Form_Field_TextField('mobile', trlKwf('Mobile')))
            ->setWidth(250);
        $fs->add(new Kwf_Form_Field_TextField('email', trlKwf('E-Mail')))
            ->setWidth(250);
        $fs->add(new Kwf_Form_Field_TextField('capacity', trlcKwf('career', 'Capacity')))
            ->setWidth(250);
        $fs->add(new Kwf_Form_Field_TextArea('annotation', trlKwf('Annotation')))
            ->setWidth(250)
            ->setHeight(70);
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->customer_id = $this->_getParam('customer_id');
    }

}