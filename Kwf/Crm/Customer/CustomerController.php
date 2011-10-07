<?php
class Vps_Crm_Customer_CustomerController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Vps_Crm_Customer_Model_Customers';

    protected function _initFields()
    {
        $fs = $this->_form->add(new Vps_Form_Container_FieldSet('Stammdaten'));
        $fs->setLabelWidth(65);

        $fs->add(new Vps_Form_Field_TextField('name', trlVps('Name').' 1'))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_TextField('name2', trlVps('Name').' 2'))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_TextField('street', trlVps('Street')))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_TextField('zip', trlVps('ZIP')))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_TextField('city', trlVps('City')))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_TextField('phone', trlVps('Phone')))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_TextField('fax', trlVps('Fax')))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_TextField('email', trlVps('E-Mail')))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_TextField('website', trlVps('Website')))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_TextArea('annotation', trlVps('Annotation')))
            ->setWidth(250)
            ->setHeight(70);
    }


}