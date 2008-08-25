<?php
class Vps_Controller_Action_User_UserController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_userDataFormName = 'Vpc_User_Edit_Form_Form';

    protected function _initFields()
    {
        $this->_form->setTable(Zend_Registry::get('userModel'));

        $fs = $this->_form->add(new Vps_Form_Container_FieldSet(trlVps('Advice')));
        $fs->setLabelWidth(80);

        $fs->add(new Vps_Form_Field_Panel())
            ->setHtml(trlVps('At the following action emails are automatically sent to the adequade user.').'<br />'
                     .trlVps('Create, Delete and E-Mail change'));
        $fs->add(new Vps_Form_Field_ShowField('password', trlVps('Activation link')))
            ->setData(new Vps_Controller_Action_User_Users_ActivationlinkData());


        $userEditForm = $this->_form->add(new $this->_userDataFormName());
        $userDirectory = Vps_Component_Data_Root::getInstance()
            ->getComponentByClass('Vpc_User_Directory_Component');
        if ($userDirectory) {
            $detailClass = Vpc_Abstract::getChildComponentClass($userDirectory->componentClass, 'detail');
            $userEditForm->addUserForms($detailClass, array('general'));
            $userEditForm->fields['firstname']->setAllowBlank(true);
            $userEditForm->fields['lastname']->setAllowBlank(true);
        } else {
            $this->_form->add(new Vpc_User_Detail_General_Form('general', null));
        }

        if ($roleField = $this->_getRoleField()) {
            $fs = $this->_form->add(new Vps_Form_Container_FieldSet(trlVps('Permissions')));
            $fs->setLabelWidth(100);
            $fs->add($roleField);
        }

        $config = Zend_Registry::get('config');
        if (isset($this->_getAuthData()->language) && $config->languages){
            $data = array();
            foreach ($config->languages as $key => $value){
                $data[$key] = $value;
            }
            $this->_form->add(new Vps_Form_Field_Select('language', trlVps('Language')))
            ->setValues($data);
        }

        $authedRole = Zend_Registry::get('userModel')->getAuthedUserRole();
        if (Vps_Registry::get('acl')->getRole($authedRole) instanceof Vps_Acl_Role_Admin) {
            $this->_form->add(new Vps_Form_Field_Checkbox('webcode', trlVps('Webcode')))
                ->setData(new Vps_Controller_Action_User_Users_WebcodeData());
        }

    }

    protected function _getRoleField()
    {
        $acl = Zend_Registry::get('acl');

        // alle erlaubten haupt-rollen in variable
        $roles = array();
        foreach ($acl->getAllowedEditRolesByRole($this->_getUserRole()) as $role) {
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
                $ret = new Vps_Form_Field_Select('role', trlVps('Rights'));
                $ret->setValues($roles)->setAllowBlank(false);


        } else {
            // eigene additionalRoles holen, nur die dürfen zugewiesen werden
            $allowedRoles = array_merge(
                $this->_getAuthData()->getAdditionalRoles(),
                $acl->getAllowedEditResourceRoleIdsByRole($this->_getUserRole())
            );

            // cards container erstellen und zu form hinzufügen
            $ret = new Vps_Form_Container_Cards('role', trlVps('Rights'));
            $ret->setAllowBlank(false);
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

                    $editor = new Vps_Form_Field_MultiCheckbox('Vps_Model_User_AdditionalRoles', trlVps('Additional rights'));
                    $editor->setColumnName('additional_role');
                    $editor->setValues($addRoles[$roleId]);

                    $card->add($editor);
                }
            }
        }
        return $ret;
    }
}
