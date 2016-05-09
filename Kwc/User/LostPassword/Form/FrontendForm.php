<?php
class Kwc_User_LostPassword_Form_FrontendForm extends Kwf_Form
{
    protected function _init()
    {
        parent::_init();
        $this->setModel(new Kwf_Model_FnF());

        $this->add(new Kwc_User_LostPassword_Form_UserEMail('email', trlKwfStatic('E-Mail')))
            ->setAllowBlank(false)
            ->setLabelWidth(50);
    }
    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
        Zend_Registry::get('userModel')->lostPassword($row->email);
    }
}
