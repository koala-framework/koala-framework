<?php
class Vps_User_UserForm extends Vps_User_Form
{
    protected function _initFields()
    {
        Vps_Form::_initFields();

        $userEditForm = $this->fields->add(new $this->_userDataFormName('user'));
        $userEditForm->setIdTemplate('{0}');

        $this->fields->add(new Vpc_User_Detail_General_Form('general', null))
            ->setIdTemplate('{0}');
    }
}
