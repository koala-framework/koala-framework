<?php
class Kwf_Crm_Customer_CustomerController extends Kwf_Controller_Action_Auto_Form
{
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Kwf_Crm_Customer_Model_Customers';

    protected function _initFields()
    {
        $fs = $this->_form->add(new Kwf_Form_Container_FieldSet('Stammdaten'));
        $fs->setLabelWidth(65);

        $fs->add(new Kwf_Form_Field_TextField('name', trlKwf('Name').' 1'))
            ->setWidth(250);
        $fs->add(new Kwf_Form_Field_TextField('name2', trlKwf('Name').' 2'))
            ->setWidth(250);
        $fs->add(new Kwf_Form_Field_TextField('street', trlKwf('Street')))
            ->setWidth(250);
        $fs->add(new Kwf_Form_Field_TextField('zip', trlKwf('ZIP')))
            ->setWidth(250);
        $fs->add(new Kwf_Form_Field_TextField('city', trlKwf('City')))
            ->setWidth(250);
        $fs->add(new Kwf_Form_Field_TextField('phone', trlKwf('Phone')))
            ->setWidth(250);
        $fs->add(new Kwf_Form_Field_TextField('fax', trlKwf('Fax')))
            ->setWidth(250);
        $fs->add(new Kwf_Form_Field_TextField('email', trlKwf('E-Mail')))
            ->setWidth(250);
        $fs->add(new Kwf_Form_Field_TextField('website', trlKwf('Website')))
            ->setWidth(250);
        $fs->add(new Kwf_Form_Field_TextArea('annotation', trlKwf('Annotation')))
            ->setWidth(250)
            ->setHeight(70);
    }


}