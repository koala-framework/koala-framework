<?php
class Kwf_Controller_Action_User_SelfController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');

    public function preDispatch()
    {
        $regUserForm = Kwf_Registry::get('config')->user->form;
        if (is_string($regUserForm)) {
            $this->_formName = $regUserForm;
        } else {
            $this->_formName = $regUserForm->self;
        }

        parent::preDispatch();
        $authedUser = Kwf_Registry::get('userModel')->getAuthedUser();
        $row = $this->_form->getModel()->getRowByKwfUser($authedUser);
        $this->_form->setId($row->id);
    }

    protected function _initFields()
    {
        parent::_initFields();

        $authedUser = Kwf_Registry::get('userModel')->getAuthedUser();
        $hasPassword = false;
        $allowPassword = true;
        foreach (Kwf_Registry::get('userModel')->getAuthMethods() as $auth) {
            if ($auth instanceof Kwf_User_Auth_Interface_Password) {
                $hasPassword = true;
            }
            if ($auth instanceof Kwf_User_Auth_Interface_Redirect) {
                if (!$auth->allowPasswordForUser($authedUser) && $allowPassword) {
                    $allowPassword = false;
                }
            }
        }

        if ($hasPassword && $allowPassword) {

            $fs = $this->_form->add(new Kwf_Form_Container_FieldSet(trlKwf('Change password')));
            $fs->setLabelWidth(130);

            $passwordField = $fs->add(new Kwf_Form_Field_Password('password1', trlKwf('Change password')));
            $validatorClass = Kwf_Registry::get('config')->user->passwordValidator;
            if ($validatorClass) {
                $passwordField->addValidator(new $validatorClass());
            }

            $fs->add(new Kwf_Form_Field_Password('password2', trlKwf('Repeat password')));

        }
    }

    protected function _hasPermissions($row, $action)
    {
        $authedUser = Kwf_Registry::get('userModel')->getAuthedUser();
        $authedUser = $this->_form->getModel()->getRowByKwfUser($authedUser);
        if (!$authedUser) return false;
        if ($authedUser->id != $row->id) {
            return false;
        }
        return true;
    }
}
