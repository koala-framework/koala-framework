<?php
class Vps_Controller_Action_User_UserController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');

    protected function _initFields()
    {
        $genders = array('male' => 'male', 'female' => 'female');

        $this->_form->setTable(Zend_Registry::get('userModel'));

        $fs0 = $this->_form->add(new Vps_Form_Container_FieldSet(trlVps('Advice')));
        $fs0->setLabelWidth(80);
        $fs0->setStyle('margin:10px;');

        $fs0->add(new Vps_Form_Field_Panel())
            ->setHtml(trlVps('At the following action emails are automatically sent to the adequade user.').'<br />'
                     .trlVps('Create, Delete and E-Mail change'));
        $fs0->add(new Vps_Form_Field_ShowField('password', trlVps('Activation link')))
            ->setData(new Vps_Controller_Action_User_Users_ActivationlinkData());

        // Hauptdaten
        $fs1 = $this->_form->add(new Vps_Form_Container_FieldSet(trlVps('Accessdata and person')));
        $fs1->setLabelWidth(100);
        $fs1->setStyle('margin:10px;');

        $editor = new Vps_Form_Field_TextField('email', trlVps('Email'));
        $editor->setVtype('email');
        $fs1->add($editor);

        $this->_addRoleField($fs1);

        $editor = new Vps_Form_Field_Select('gender', trlVps('Gender'));
        $editor->setValues($genders)
               ->setAllowBlank(false);
        $fs1->add($editor);



        $fs1->add(new Vps_Form_Field_TextField('title', trlVps('Title')));
        $fs1->add(new Vps_Form_Field_TextField('firstname', trlVps('First name')));
        $fs1->add(new Vps_Form_Field_TextField('lastname', trlVps('Last name')));

        $config = Zend_Registry::get('config');
        if (isset($this->_getAuthData()->language) && $config->languages){
            $data = array();
            foreach ($config->languages as $key => $value){
                $data[$key] = $value;
            }
            $fs1->add(new Vps_Form_Field_Select('language', trlVps('Language')))
            ->setValues($data);
        }

        try {
            new Vpc_Forum_User_Model();
            $fs1->add(new Vps_Auto_Field_TextField('nickname', trlVps('Forum name')))
                ->setData(new Vps_Controller_Action_User_Users_ForumNameData());
        } catch(Zend_Db_Statement_Exception $e) {
            // Forum user table existiert nicht -> daten nicht anzeigen
        }

        $authedRole = Zend_Registry::get('userModel')->getAuthedUserRole();
        $acl = Zend_Registry::get('acl');
        if ($acl->getRole($authedRole) instanceof Vps_Acl_Role_Admin) {
            $fs1->add(new Vps_Form_Field_Checkbox('webcode', trlVps('Webcode')))
                ->setData(new Vps_Controller_Action_User_Users_WebcodeData());
        }

    }

    protected function _addRoleField($addTo)
    {
        $acl = Zend_Registry::get('acl');

        // alle erlaubten haupt-rollen in variable
        $roles = array();
        foreach ($acl->getAllowedEditRolesByRole($this->_getUserRole()) as $role) {
            $roles[$role->getRoleId()] = $role->getRoleName();
        }
        if (!$roles) return;

        // ALLE additional roles in variable
        $addRoles = array();
        foreach ($acl->getAdditionalRoles() as $role) {
            $addRoles[$role->getParentRoleId()][$role->getRoleId()] = $role->getRoleName();
        }

        // Wenns keine additionalRoles gibt, normales select verwenden
        if (!$addRoles) {
            $editor = new Vps_Form_Field_Select('role', trlVps('Rights'));
            $editor->setValues($roles)
                ->setAllowBlank(false);
            $addTo->add($editor);
        } else {
            // eigene additionalRoles holen, nur die dürfen zugewiesen werden
            $allowedRoles = array_merge(
                $this->_getAuthData()->getAdditionalRoles(),
                $acl->getAllowedEditResourceRoleIdsByRole($this->_getUserRole())
            );

            // cards container erstellen und zu form hinzufügen
            $cards = $addTo->add(new Vps_Form_Container_Cards('role', trlVps('Rights')))
                ->setAllowBlank(false);
            foreach ($roles as $roleId => $roleName) {
                $card = $cards->add();
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
    }
}
