<?php
class Vps_Controller_Action_User_Users_RoleData extends Vps_Auto_Data_Abstract
{
    private $_roles;

    public function __construct()
    {
        $acl = Zend_Registry::get('acl');
        $this->_roles = array();
        foreach($acl->getRoles() as $role) {
            if($role instanceof Vps_Acl_Role) {
                $this->_roles[$role->getRoleId()] = $role->getRoleName();
            }
        }
    }

    public function load($row)
    {
        if (isset($this->_roles[$row->role])) {
            return $this->_roles[$row->role];
        } else {
            return $row->role;
        }
    }
}

class Vps_Controller_Action_User_UsersController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array('add'=>true, 'delete'=>true);
    protected $_sortable = true;
    protected $_defaultOrder = 'id';
    protected $_paging = 20;
    protected $_editDialog = array('controllerUrl'=>'/vps/user/user',
                                   'width'=>400,
                                   'height'=>300);

    protected function _initColumns()
    {
        $this->_table = Zend_Registry::get('userModel');

        $this->_columns->add(new Vps_Auto_Grid_Column_Button('edit', 'Edit'));
        $this->_columns->add(new Vps_Auto_Grid_Column('email', 'Email', 140));
        $this->_columns->add(new Vps_Auto_Grid_Column('role', 'Rights'))
             ->setData(new Vps_Controller_Action_User_Users_RoleData());
        $this->_columns->add(new Vps_Auto_Grid_Column('gender', 'Gender', 200));
        $this->_columns->add(new Vps_Auto_Grid_Column('title', 'Title', 100));
        $this->_columns->add(new Vps_Auto_Grid_Column('firstname', 'First name', 200));
        $this->_columns->add(new Vps_Auto_Grid_Column('lastname', 'Last name', 200));
    }

}
