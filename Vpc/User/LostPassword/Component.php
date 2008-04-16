<?php
class Vpc_User_LostPassword_Component extends Vpc_Formular_Component
{
    protected function _init()
    {
        parent::_init();

        $fieldSettings = array('name'  => 'email',
                               'width' => 200);
        $c = $this->_createFieldComponent('Textbox', $fieldSettings);
        $c->store('name', 'email');
        $c->store('fieldLabel', 'Email');
        $c->store('isMandatory', true);

        $c = $this->_createFieldComponent('Submit', array(
            'name'=>'sbmt', 'width'=>100, 'text' => 'Senden'
        ));
        $c->store('name', 'sbmt');
        $c->store('fieldLabel', '&nbsp;');
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['formTemplate'] = Vpc_Admin::getComponentFile('Vpc_Formular_Component', '', 'tpl');
        return $ret;
    }

    protected function _processForm()
    {
        $values = array();
        foreach ($this->getChildComponents() as $c) {
            if ($c instanceof Vpc_Formular_Field_Interface) {
                $name = $c->getStore('name');
                if ($name == 'email') {
                    $values[$name] = $c->getValue();
                }
            }
        }

        Zend_Registry::get('userModel')->lostPassword($values['email']);
    }
}