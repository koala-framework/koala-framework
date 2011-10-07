<?php
class Vps_Controller_Action_User_SelfController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');

    public function preDispatch()
    {
        $regUserForm = Vps_Registry::get('config')->user->form;
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

        $fs = $this->_form->add(new Vps_Form_Container_FieldSet(trlVps('Change password')));
        $fs->setLabelWidth(130);

        $fs->add(new Vps_Form_Field_Password('password1', trlVps('Change password')));
        $fs->add(new Vps_Form_Field_Password('password2', trlVps('Repeat password')));
    }

    protected function _hasPermissions($row, $action)
    {
        $userId = $this->_getParam('id');
        if (!$userId) {
            return false;
        }

        $authedUser = Vps_Registry::get('userModel')->getAuthedUser();
        if ($authedUser->id != $userId || $authedUser->id != $row->id) {
            return false;
        }
        return true;
    }
}