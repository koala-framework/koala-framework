<?php
class Kwf_Controller_Action_User_UserController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');

    private $_permissionFieldset;

    public function preDispatch()
    {
        $regUserForm = Kwf_Registry::get('config')->user->form;
        if (is_string($regUserForm)) {
            $this->_formName = $regUserForm;
        } else {
            $this->_formName = $regUserForm->grid;
        }
        parent::preDispatch();
    }

    protected function _initFields()
    {
        parent::_initFields();

        $fs = $this->_form->prepend(new Kwf_Form_Container_FieldSet(trlKwf('Advice')));
        $fs->setLabelWidth(100);

        $fs->add(new Kwf_Form_Field_Panel())
            ->setHtml(trlKwf('After following actions emails are sent automatically to the respective user:').'<br />'
                     .trlKwf('Create, Delete and E-Mail change'));
        $fs->add(new Kwf_Form_Field_ShowField('password', trlKwf('Activation link')))
            ->setData(new Kwf_Controller_Action_User_Users_ActivationlinkData());
        $fs->add(new Kwf_Form_Field_Checkbox('avoid_mailsend', trlKwf('E-Mails')))
            ->setSave(false)
            ->setBoxLabel(trlKwf("Don't send any E-Mail when saving."));

        if ($roleField = $this->_getRoleField()) {
            $this->_getPermissionFieldset()->add($roleField);
        }

        $authedRole = Zend_Registry::get('userModel')->getAuthedUserRole();
        if (Kwf_Registry::get('acl')->getRole($authedRole) instanceof Kwf_Acl_Role_Admin) {
            $this->_getPermissionFieldset()->add(new Kwf_Form_Field_Checkbox('webcode', trlKwf('Only for this web')))
                ->setData(new Kwf_Controller_Action_User_Users_WebcodeData())
                ->setHelpText(trlKwf('If this box is checked, the account may only be used for this web. If you wish to use the same account for another web, do not check this box.'));
        }

        $fs = $this->_form->add(new Kwf_Form_Container_FieldSet(trlKwf('Statistics')));
        $fs->setLabelWidth(100);
        $fs->add(new Kwf_Form_Field_ShowField('logins', trlKwf('Logins')));
        $fs->add(new Kwf_Form_Field_ShowField('last_login', trlKwf('Last login')))
            ->setTpl('{value:localizedDatetime}');
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeSave($row);
        if ($this->_getParam('avoid_mailsend')) {
            $row->setSendMails(false);
        }
    }

    private function _getPermissionFieldset()
    {
        if (!$this->_permissionFieldset) {
            $this->_permissionFieldset = $this->_form->add(new Kwf_Form_Container_FieldSet(trlKwf('Permissions')));
            $this->_permissionFieldset->setLabelWidth(100);
            $this->_permissionFieldset->setName('permissions');
        }
        return $this->_permissionFieldset;
    }

    protected function _getRoleField()
    {
        $acl = Kwf_Registry::get('acl');
        $userRole = Kwf_Registry::get('userModel')->getAuthedUserRole();
        $authedUser = Kwf_Registry::get('userModel')->getAuthedUser();

        // alle erlaubten haupt-rollen in variable
        $roles = array();
        foreach ($acl->getAllowedEditRolesByRole($userRole) as $role) {
            $roles[$role->getRoleId()] = $role->getRoleName();
        }
        if (!$roles) return null;

        // ALLE additional roles in variable
        $addRoles = array();
        foreach ($acl->getAdditionalRoles() as $role) {
            $addRoles[$role->getParentRoleId()][$role->getRoleId()] = $role->getRoleName();
        }


        // Wenns keine additionalRoles gibt, normales select verwenden
        if (!$addRoles) {
            $ret = new Kwf_Form_Field_Select('role', trlKwf('Rights'));
            $ret->setValues($roles)->setAllowBlank(false);
        } else {
            // eigene additionalRoles holen, nur die dürfen zugewiesen werden
            $allowedRoles = array_merge(
                $authedUser->getAdditionalRoles(),
                $acl->getAllowedEditResourceRoleIdsByRole($userRole)
            );

            // cards container erstellen und zu form hinzufügen
            $ret = new Kwf_Form_Container_Cards('role', trlKwf('Rights'));
            $ret->getCombobox()->setAllowBlank(false);
            foreach ($roles as $roleId => $roleName) {
                $card = $ret->add();
                $card->setTitle($roleName);
                $card->setName($roleId);

                if (isset($addRoles[$roleId])) {
                    foreach ($addRoles[$roleId] as $addRoleId => $addRoleName) {
                        if (!in_array($addRoleId, $allowedRoles)) {
                            unset($addRoles[$roleId][$addRoleId]);
                        }
                    }
                    $editor = new Kwf_Form_Field_MultiCheckboxLegacy('Kwf_User_AdditionalRoles', trlKwf('Additional rights'));
                    $editor->setColumnName('additional_role')
                        ->setValues($addRoles[$roleId])
                        ->setReferences(array(
                            'columns' => array('user_id'),
                            'refColumns' => array('id')
                        ));
                    $card->add($editor);
                }
            }
        }
        return $ret;
    }

    protected function _hasPermissions($row, $action)
    {
        if (!$row) {
            return true;
        }

        $acl = Kwf_Registry::get('acl');
        $userRole = Kwf_Registry::get('userModel')->getAuthedUserRole();

        if (!($acl->getRole($userRole) instanceof Kwf_Acl_Role_Admin)) { //admin always sees all roles

            $roles = array();
            foreach ($acl->getAllowedEditRolesByRole($userRole) as $role) {
                $roles[$role->getRoleId()] = $role->getRoleName();
            }
            if (!$roles) return false;

            if (!$row || !array_key_exists($row->role, $roles)) {
                return false;
            }
        }

        return true;
    }
}
