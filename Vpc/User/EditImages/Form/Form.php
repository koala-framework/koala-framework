<?php
class Vpc_User_EditImages_Form_Form extends Vps_Form
{
    protected function _init()
    {
        parent::_init();
        $this->setTable(Zend_Registry::get('userModel'));

        $this->add(Vpc_Abstract_Form::createComponentFormByDbIdTemplate('users_{0}-images'));
    }
}
