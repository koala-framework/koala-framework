<?php
class Vps_Controller_Action_User_UserController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save'=>true, 'add'=>true);

    protected function _initFields()
    {
        $genders = array('male' => 'male', 'female' => 'female');

        $this->_form->setTable(Zend_Registry::get('userModel'));

        $fs0 = $this->_form->add(new Vps_Auto_Container_FieldSet('Hinweis'));
        $fs0->setLabelWidth(80);
        $fs0->setStyle('margin:10px;');

        $fs0->add(new Vps_Auto_Field_Panel())
            ->setHtml('Bei folgenden Aktionen werden automatisch E-Mails an den '
                     .'betreffenden Benutzer gesendet.<br />'
                     .'Erstellen, Löschen und E-Mail ändern');

        // Hauptdaten
        $fs1 = $this->_form->add(new Vps_Auto_Container_FieldSet('Zugangsdaten &amp; Person'));
        $fs1->setLabelWidth(80);
        $fs1->setStyle('margin:10px;');

        $editor = new Vps_Auto_Field_TextField('email', 'Email');
        $editor->setVtype('email');
        $fs1->add($editor);

        $this->_addRoleField($fs1);

        $editor = new Vps_Auto_Field_Select('gender', 'Gender');
        $editor->setValues($genders)
               ->setAllowBlank(false);
        $fs1->add($editor);

        $fs1->add(new Vps_Auto_Field_TextField('title', 'Title'));
        $fs1->add(new Vps_Auto_Field_TextField('firstname', 'First name'));
        $fs1->add(new Vps_Auto_Field_TextField('lastname', 'Last name'));

        $authedRole = Zend_Registry::get('userModel')->getAuthedUserRole();
        $acl = Zend_Registry::get('acl');
        if ($acl->getRole($authedRole) instanceof Vps_Acl_Role_Admin) {
            $fs1->add(new Vps_Auto_Field_Checkbox('webcode', 'Webcode'))
                ->setData(new Vps_Controller_Action_User_Users_WebcodeData());
        }

    }

    protected function _addRoleField($fs1)
    {
        $acl = Zend_Registry::get('acl');
        $roles = array();
        foreach($acl->getRoles() as $role) {
            if($role instanceof Vps_Acl_Role) {
                $roles[$role->getRoleId()] = $role->getRoleName();
            }
        }

        $editor = new Vps_Auto_Field_Select('role', 'Rights');
        $editor->setValues($roles)
               ->setAllowBlank(false);
        $fs1->add($editor);
    }


}