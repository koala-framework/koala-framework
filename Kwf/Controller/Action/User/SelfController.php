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
    }

    protected function _initFields()
    {
        parent::_initFields();

        $fs = $this->_form->add(new Kwf_Form_Container_FieldSet(trlKwf('Change password')));
        $fs->setLabelWidth(130);

        $fs->add(new Kwf_Form_Field_Password('password1', trlKwf('Change password')));
        $fs->add(new Kwf_Form_Field_Password('password2', trlKwf('Repeat password')));
    }

    protected function _hasPermissions($row, $action)
    {
        $userId = $this->_getParam('id');
        if (!$userId) {
            return false;
        }

        $authedUser = Kwf_Registry::get('userModel')->getAuthedUser();
        if ($authedUser->id != $userId || $authedUser->id != $row->id) {
            return false;
        }
        return true;
    }
}