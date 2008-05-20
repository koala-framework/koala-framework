<?php
class Vps_Form_Field_Abstract_Controller extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array('save', 'saveBack');

    protected function _initFields()
    {
        $this->_form = Vpc_Abstract_Form::createComponentForm('form', $this->class);

        $this->_form->setModel(new Vps_Model_Field(array(
            'parentModel' => new Vps_Model_Db(array(
                                'table' => new Vpc_Formular_Dynamic_Model()
                            )),
            'fieldName' => 'settings',
            'default' => Vpc_Abstract::getSetting($this->class, 'default')
        )));
        //TODO: recht unschön :D
        if (preg_match('#[0-9]*$#', $this->componentId, $m)) {
            $this->_form->setId($m[0]);
        }
    }
    public function jsonIndexAction()
    {
        $this->view->vpc(Vpc_Admin::getInstance($this->class)->getExtConfig());
    }
}
