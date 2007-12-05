<?php
class Vps_Controller_Action_Component_Useredit extends Vps_Controller_Action_Auto_Form
{
    protected $_permissions = array('save' => true, 'add' => true);
    protected $_tableName = 'Vps_Model_User_Users';
    
    protected function _initFields()
    {
        $acl = Zend_Registry::get('acl');
        $roles = array();
        foreach ($acl->getRoles() as $role) {
            if ($role instanceof Vps_Acl_Role) {
                $roles[$role->getRoleId()] = $role->getRoleName();
            }
        }
        
        $fields = $this->_form->fields;
        $fields->add(new Vps_Auto_Field_TextField('username', 'Username'))
            ->setAllowBlank(false);
        $fields->add(new Vps_Auto_Field_Select('role', 'Role'))
            ->setValues($roles)
            ->setTriggerAction('all');
        $fields->add(new Vps_Auto_Field_TextField('email', 'E-Mail'))
            ->setAllowBlank(false);
    }
    
    public function jsonMailsendAction()
    {
        $success = false;
        $id = $this->_getParam('id');
        $table = new $this->_tableName;
        if ($user = $table->find($id)->current()) {
            if ($user->email) {
                $user->sendPasswordMail();
                $user->save();
                $success = true;
            } else {
                $error = 'E-Mail wurde nicht gesendet, da keine E-Mail-Adresse für diesen Benutzer gefunden wurde.<br><br>'
                        .'Das alte Passwort bleibt erhalten.';
                $this->view->error = $error;
            }
        }
        $this->view->success = $success;
    }
    
    public function _beforeSave($row)
    {
//        $row->active = 1;
    }
    
    public function indexAction()
    {
        $this->view->ext('Vps.User.Useredit');
    }
}
