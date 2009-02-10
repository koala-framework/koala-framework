<?php
class Vps_Controller_Action_User_UsersController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array('add'); //delete button entfernt
    protected $_sortable = true;
    protected $_defaultOrder = 'id';
    protected $_paging = 20;
    protected $_editDialog = array('controllerUrl'=>'/vps/user/user',
                                   'width'=>500,
                                   'height'=>410);
    protected $_filters = array('text' => true);

    protected function _getSelect()
    {
        $select = parent::_getSelect();
        $acl = Zend_Registry::get('acl');
        $roles = array();
        foreach ($acl->getAllResources() as $res) {
            if ($res instanceof Vps_Acl_Resource_EditRole
                && $acl->isAllowed($this->_getUserRole(), $res, 'view')
            ) {
                $roles[] = $res->getRoleId();
            }
        }

        if ($roles) {
            $select->whereEquals('role', $roles);
        } else {
            $select = null;
        }

        return $select;
    }

    public function preDispatch()
    {
        $this->_model = Zend_Registry::get('userModel');
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

        $this->_columns->add(new Vps_Grid_Column('gender', trlVps('Gender'), 70))
            ->setRenderer('genderIcon');
        $this->_columns->add(new Vps_Grid_Column('title', trlVps('Title'), 100));

        $this->_columns->add(new Vps_Grid_Column('firstname', trlVps('First name'), 150));
        $this->_columns->add(new Vps_Grid_Column('lastname', trlVps('Last name'), 150));

        if (isset($this->_getAuthData()->language)){
             $this->_columns->add(new Vps_Grid_Column('language', trlVps('lang'), 30));
        }

        $this->_columns->add(new Vps_Grid_Column('password', trlVps('Activated'), 60))
            ->setRenderer('boolean');

        $authedRole = Zend_Registry::get('userModel')->getAuthedUserRole();
        $acl = Zend_Registry::get('acl');
        if ($acl->getRole($authedRole) instanceof Vps_Acl_Role_Admin) {
            $this->_columns->add(new Vps_Grid_Column_Checkbox('webcode', trlVps('Webcode'), 60))
                 ->setData(new Vps_Controller_Action_User_Users_WebcodeData());
        }
    }
}
