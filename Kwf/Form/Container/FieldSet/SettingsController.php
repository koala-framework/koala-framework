<?php
class Vps_Form_Container_FieldSet_SettingsController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array();
    protected $_permissions = array('save');

    protected function _initFields()
    {
        $this->_form = new Vps_Form_Container_FieldSet_Form();

        $this->_form->setModel(new Vps_Model_Field(array(
            'parentModel' => new Vps_Model_Db(array(
                                'table' => new Vpc_Formular_Dynamic_Model()
                            )),
            'fieldName' => 'settings'
        )));
        //TODO: recht unschön :D
        if (preg_match('#[0-9]*$#', $this->componentId, $m)) {
            $this->_form->setId($m[0]);
        }

        parent::_initFields();
    }
}
