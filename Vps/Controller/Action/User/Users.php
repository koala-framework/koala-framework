<?php
class Vps_Controller_Action_User_Users_RoleColumn extends Vps_Auto_Grid_Column
{
    private $_roles;
    public function load($row, $role)
    {
        if (isset($this->_roles[$row->role])) {
            return $this->_roles[$row->role];
        } else {
            return $row->role;
        }
    }
    public function setRoles($roles)
    {
        $this->_roles = $roles;
    }
}

class Vps_Controller_Action_User_Users extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array('save'=>true,
                                'add'=>true,
                                'delete'=>true);
    protected $_paging = 0;
    protected $_defaultOrder = 'username';
    protected $_tableName = 'Vps_Model_User_Users';

    protected function _initColumns()
    {
        $acl = Zend_Registry::get('acl');
        $roles = array();
        foreach($acl->getRoles() as $role) {
            if($role instanceof Vps_Acl_Role) {
                $roles[$role->getRoleId()] = $role->getRoleName();
            }
        }

        $this->_columns->add(new Vps_Auto_Grid_Column('active', 'Active', 40))
                ->setEditor(new Vps_Auto_Field_Checkbox());
        $this->_columns->add(new Vps_Auto_Grid_Column('username', 'Username', 140))
                ->setEditor(new Vps_Auto_Field_TextField());

        $this->_columns->add(new Vps_Controller_Action_User_Users_RoleColumn('role_name'))
                             ->setRoles($roles);

        $editor = new Vps_Auto_Field_ComboBox();
        $editor->setValues($roles)
               ->setEditable(false)
               ->setTriggerAction('all')
               ->setLazyRender(true);
        $this->_columns->add(new Vps_Auto_Grid_Column('role', 'Rights'))
                ->setShowDataIndex('role_name')
                ->setEditor($editor);

        $this->_columns->add(new Vps_Auto_Grid_Column('realname', 'Name', 200))
                ->setEditor(new Vps_Auto_Field_TextField());

        $this->_columns->add(new Vps_Auto_Grid_Column('email', 'E-Mail', 250))
                ->setEditor(new Vps_Auto_Field_TextField())
                ->getEditor()->setVtype('email');
            
        $this->_columns->add(new Vps_Auto_Grid_Column('password_mailed', 'Password mailed', 40))
                ->setRenderer('boolean');
    }

    public function indexAction()
    {
        $this->view->ext('Vps.User.Users');
    }

    public function jsonMailsendAction()
    {
        $success = false;
        $request = $this->getRequest();
        $id = $request->getParam('id');

        if ($user = $this->_table->find($id)->current()) {
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
}
