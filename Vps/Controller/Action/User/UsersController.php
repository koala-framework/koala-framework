<?php
class Vps_Controller_Action_User_UsersController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array('add', 'delete');
    protected $_sortable = true;
    protected $_defaultOrder = 'id';
    protected $_paging = 20;
    protected $_editDialog = array('controllerUrl'=>'/vps/user/user',
                                   'width'=>400,
                                   'height'=>410);

    protected function _getWhere()
    {
        $where = parent::_getWhere();
        $acl = Zend_Registry::get('acl');
        if (!($acl->getRole($this->_getUserRole()) instanceof Vps_Acl_Role_Admin)) {
            foreach ($acl->getRoles() as $role) {
                if ($role instanceof Vps_Acl_Role && !($role instanceof Vps_Acl_Role_Admin)) {
                    $roles[] = $role->getRoleId();
                }
            }
            $where[] = "role IN ('".implode("', '", $roles)."')";
        }
        return $where;
    }

    public function preDispatch()
    {
        $this->_table = Zend_Registry::get('userModel');
        $this->_table->createAllCache();
        parent::preDispatch();
    }

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column_Button('edit', trlVps('Edit')));
        $this->_columns->add(new Vps_Grid_Column('id', 'ID', 50));
        $this->_columns->add(new Vps_Grid_Column('email', trlVps('Email'), 140));
        $this->_columns->add(new Vps_Grid_Column('role', trlVps('Rights')))
            ->setData(new Vps_Controller_Action_User_Users_RoleData());

        $this->_columns->add(new Vps_Grid_Column('gender', trlVps('Gender'), 50));
        $this->_columns->add(new Vps_Grid_Column('title', trlVps('Title'), 100));

        $this->_columns->add(new Vps_Grid_Column('firstname', trlVps('First name'), 150));
        $this->_columns->add(new Vps_Grid_Column('lastname', trlVps('Last name'), 150));

        try {
            new Vpc_Forum_User_Model();
            $this->_columns->add(new Vps_Auto_Grid_Column('nickname', trlVps('Forum name'), 150))
                ->setData(new Vps_Controller_Action_User_Users_ForumNameData());
        } catch(Zend_Db_Statement_Exception $e) {
            // Forum user table existiert nicht -> daten nicht anzeigen
        }

        if (isset($this->_getAuthData()->language)){
             $this->_columns->add(new Vps_Grid_Column('language', trlVps('lang'), 30));
        }

        $this->_columns->add(new Vps_Auto_Grid_Column('password', trlVps('Activated'), 60))
            ->setRenderer('boolean');

        $authedRole = Zend_Registry::get('userModel')->getAuthedUserRole();
        $acl = Zend_Registry::get('acl');
        if ($acl->getRole($authedRole) instanceof Vps_Acl_Role_Admin) {
            $this->_columns->add(new Vps_Grid_Column('webcode', trlVps('Webcode'), 60))
                 ->setData(new Vps_Controller_Action_User_Users_WebcodeData())
                 ->setRenderer('boolean');
        }
    }
}
