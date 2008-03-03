<?php
class Vpc_User_Edit_Component extends Vpc_User_Abstract_Form
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['success'] = 'Vpc_User_Edit_Success_Component';
        return $ret;
    }

    protected function _init()
    {
        parent::_init();

        $c = $this->_createFieldComponent('Submit', array(
            'name'=>'sbmt', 'width'=>200, 'text' => 'Account bearbeiten'
        ));
        $c->store('name', 'sbmt');
        $c->store('fieldLabel', '&nbsp;');
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['formTemplate'] = Vpc_Admin::getComponentFile('Vpc_Formular_Component', '', 'tpl');
        $ret['email'] = $this->_getEditRow()->email;
        return $ret;
    }

    protected function _getEditRow()
    {
        $table = Zend_Registry::get('userModel');
        return $table->getAuthedUser();
    }
}
