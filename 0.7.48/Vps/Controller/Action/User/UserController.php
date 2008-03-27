<?php
class Vps_Controller_Action_User_UserController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save'=>true, 'add'=>true);

    public function jsonSaveAction()
    {
        try {
            parent::jsonSaveAction();
        } catch(Vps_ClientException $e) {
            $this->view->error = $e->getMessage();
        }
    }

    protected function _initFields()
    {
        $genders = array('male' => 'male', 'female' => 'female');

        $this->_form->setTable(Zend_Registry::get('userModel'));

        $fs0 = $this->_form->add(new Vps_Auto_Container_FieldSet(trlVps('Advice')));
        $fs0->setLabelWidth(80);
        $fs0->setStyle('margin:10px;');

        $fs0->add(new Vps_Auto_Field_Panel())
            ->setHtml(trlVps('At the following action emails are automatically sent to the adequade user.').'<br />'
                     .trlVps('Create, Delete and E-Mail change'));

        // Hauptdaten
        $fs1 = $this->_form->add(new Vps_Auto_Container_FieldSet(trlVps('Accessdata and person')));
        $fs1->setLabelWidth(80);
        $fs1->setStyle('margin:10px;');

        $editor = new Vps_Auto_Field_TextField('email', trlVps('Email'));
        $editor->setVtype('email');
        $fs1->add($editor);

        $this->_addRoleField($fs1);

        $editor = new Vps_Auto_Field_Select('gender', trlVps('Gender'));
        $editor->setValues($genders)
               ->setAllowBlank(false);
        $fs1->add($editor);



        $fs1->add(new Vps_Auto_Field_TextField('title', trlVps('Title')));
        $fs1->add(new Vps_Auto_Field_TextField('firstname', trlVps('First name')));
        $fs1->add(new Vps_Auto_Field_TextField('lastname', trlVps('Last name')));

        if (isset($this->_getAuthData()->language)){
            $config = Zend_Registry::get('config');
            $data = array();
            foreach ($config->languages as $key => $value){
                $data[$key] = $value;
            }
            $fs1->add(new Vps_Auto_Field_Select('language', trlVps('Language')))
            ->setValues($data);
        }

        $authedRole = Zend_Registry::get('userModel')->getAuthedUserRole();
        $acl = Zend_Registry::get('acl');
        if ($acl->getRole($authedRole) instanceof Vps_Acl_Role_Admin) {
            $fs1->add(new Vps_Auto_Field_Checkbox('webcode', trlVps('Webcode')))
                ->setData(new Vps_Controller_Action_User_Users_WebcodeData());
        }

    }

    protected function _addRoleField($fs1)
    {
        $acl = Zend_Registry::get('acl');
        $roles = array();
        foreach ($acl->getRoles() as $role) {
            if ($role instanceof Vps_Acl_Role
                && ( !($role instanceof Vps_Acl_Role_Admin)
                || ($acl->getRole($this->_getUserRole()) instanceof Vps_Acl_Role_Admin) )
            ) {
                $roles[$role->getRoleId()] = $role->getRoleName();
            }
        }

        $editor = new Vps_Auto_Field_Select('role', trlVps('Rights'));
        $editor->setValues($roles)
               ->setAllowBlank(false);
        $fs1->add($editor);
    }


}