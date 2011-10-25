<?php
class Kwf_Form_Field_Abstract_Controller extends Kwf_Controller_Action_Auto_Form
{
    protected $_buttons = array('save', 'saveBack');

    protected function _initFields()
    {
        $this->_form = Kwc_Abstract_Form::createComponentForm($this->class);

        $this->_form->setModel(new Kwf_Model_Field(array(
            'parentModel' => new Kwf_Model_Db(array(
                                'table' => new Kwc_Formular_Dynamic_Model()
                            )),
            'fieldName' => 'settings',
            'default' => Kwc_Abstract::getSetting($this->class, 'default')
        )));
        //TODO: recht unschön :D
        if (preg_match('#[0-9]*$#', $this->componentId, $m)) {
            $this->_form->setId($m[0]);
        }
    }
    public function jsonIndexAction()
    {
        $this->view->kwc(Kwc_Admin::getInstance($this->class)->getExtConfig());
    }
}
