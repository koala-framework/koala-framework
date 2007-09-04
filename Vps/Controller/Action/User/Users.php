<?php

class Vps_Controller_Action_User_Users extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array('save'=>true,
                                'add'=>true,
                                'delete'=>true);
    protected $_paging = 0;
    protected $_defaultOrder = 'username';
    protected $_tableName = 'Vps_Model_User_Users';
    protected $_roles = array();

    protected function _initColumns()
    {
        $acl = Zend_Registry::get('acl');
        $roles = $acl->getRoles();
        foreach($roles as $role) {
            if($role instanceof Vps_Acl_Role) {
                $this->_roles[$role->getRoleId()] = $role->getRoleName();
            }
        }

        $this->_columns->add(new Vps_Auto_Grid_Column('active', 'Active', 40))
                ->setRenderer('Boolean')
                ->setEditor(new Vps_Auto_Field_Checkbox());
        $this->_columns->add(new Vps_Auto_Grid_Column('username', 'Username', 140))
                ->setEditor(new Vps_Auto_Field_TextField());

        $this->_columns->add(new Vps_Auto_Grid_Column('role_name'));

        $editor = new Vps_Auto_Field_ComboBox();
        $editor->setStoreData($this->_roles)
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
                ->setRenderer('Boolean');
    }

    public function indexAction()
    {
        $this->view->ext('Vps.User.Users');
    }

    protected function _fetchFromRow($row, $field)
    {
        if ($field == 'role_name') {
            if (isset($this->_roles[$row->role])) {
                return $this->_roles[$row->role];
            } else {
                return $row->role;
            }
        }
        return parent::_fetchFromRow($row, $field);
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
