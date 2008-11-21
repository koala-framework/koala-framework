<?php
class Vps_Crm_Customer_ContactpersonController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add', 'delete');
    protected $_modelName = 'Vps_Crm_Customer_Model_Contactpersons';

    protected function _initFields()
    {
        $fs = $this->_form->add(new Vps_Form_Container_FieldSet(trlVps('Ansprechpartner')));
        $fs->setLabelWidth(50);
        $fs->setStyle('margin:10px;');

        $fs->add(new Vps_Form_Field_TextField('firstname', trlVps('Vorname')))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_TextField('lastname', trlVps('Zuname')))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_TextField('phone', trlVps('Telefon')))
            ->setWidth(250);
        $fs->add(new Vps_Form_Field_TextField('email', trlVps('E-Mail')))
            ->setWidth(250);
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->customer_id = $this->_getParam('customer_id');
    }

}