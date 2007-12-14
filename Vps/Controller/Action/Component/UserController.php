<?php
class Vps_Controller_Action_Component_UserController extends Vps_Controller_Action_User_Users
{
    protected $_buttons = array('save'=>true,
                                'add'=>true,
                                'delete'=>true);
    protected $_paging = 20;
    protected $_defaultOrder = 'username';
    protected $_editDialog = array(
        'type'          => 'Vps.User.Useredit'
    );
    
    protected function _initColumns()
    {
        $this->_table = Zend_Registry::get('userModel');

        $acl = Zend_Registry::get('acl');
        $roles = array();
        foreach ($acl->getRoles() as $role) {
            if ($role instanceof Vps_Acl_Role) {
                $roles[$role->getRoleId()] = $role->getRoleName();
            }
        }

        $this->_columns->add(new Vps_Auto_Grid_Column('id'));
        $this->_columns->add(new Vps_Auto_Grid_Column('active', 'Active', 40));
        $this->_columns->add(new Vps_Auto_Grid_Column('username', 'Username', 140));
        $this->_columns->add(new Vps_Controller_Action_User_Users_RoleColumn('role_name'))
                             ->setRoles($roles);
        $this->_columns->add(new Vps_Auto_Grid_Column('realname', 'Name', 200));
        $this->_columns->add(new Vps_Auto_Grid_Column('email', 'E-Mail', 250));
        $this->_columns->add(new Vps_Auto_Grid_Column('password_mailed', 'Password mailed', 40))
                ->setRenderer('boolean');
    }

    public function indexAction()
    {
        $config = array(
            'controllerUrl' => '/admin/component/user'
        );
        $this->view->ext('Vps.User.Users', $config);
    }

}
