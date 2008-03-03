<?php
class Vpc_User_Register_Component extends Vpc_User_Abstract_Form
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['standardRole']  = 'guest';
        $ret['childComponentClasses']['success'] = 'Vpc_User_Register_Success_Component';
        return $ret;
    }

    protected function _init()
    {
        parent::_init();

        $c = $this->_createFieldComponent('Submit', array(
            'name'=>'sbmt', 'width'=>200, 'text' => 'Create Account'
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

    protected function _beforeSave($row)
    {
        $row->role = $this->_getSetting('standardRole');
    }

    protected function _getEditRow()
    {
        $table = Zend_Registry::get('userModel');
        return $table->createRow();
    }

    protected function _processForm()
    {
        $email = '';
        foreach ($this->getChildComponents() as $c) {
            if ($c instanceof Vpc_Formular_Field_Interface) {
                $name = $c->getStore('name');
                if ($name == 'email') {
                    $email = $c->getValue();
                }
            }
        }

        if ($email) {
            $existsRow = Zend_Registry::get('userModel')->fetchRowByEmail($email);
            if ($existsRow) {
                throw new Vps_ClientException('Ein Benutzer mit dieser Email-Adresse existiert bereits.');
            }
        }

        parent::_processForm();
    }
}
