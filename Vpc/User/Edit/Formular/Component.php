<?php
class Vpc_User_Edit_Formular_Component extends Vpc_Formular_Component
{
    protected $_formName = 'Vpc_User_Edit_Formular_Form';
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlVps('edit account');
        $ret['generators']['child']['component']['success'] = 'Vpc_User_Edit_Formular_Success_Component';
        return $ret;
    }
    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setId(Zend_Registry::get('userModel')->getAuthedUser()->id);
    }
}
