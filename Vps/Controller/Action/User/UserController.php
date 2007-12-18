<?php
class Vps_Controller_Action_User_UserController extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save'=>true, 'add'=>true);

    protected function _initFields()
    {
        $genders = array('male' => 'male', 'female' => 'female');

        $this->_form->setTable(Zend_Registry::get('userModel'));

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