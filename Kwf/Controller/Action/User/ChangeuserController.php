<?php
class Kwf_Controller_Action_User_ChangeuserController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_defaultOrder = 'lastname';
    protected $_paging = 10;

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $authedChangedRole = Zend_Registry::get('userModel')->getAuthedChangedUserRole();
        $acl = Zend_Registry::get('acl');
        if (!($acl->getRole($authedChangedRole) instanceof Kwf_Acl_Role_Admin)) {
            //wenn nicht superuser
            foreach ($acl->getRoles() as $role) {
                if ($role instanceof Kwf_Acl_Role && !($role instanceof Kwf_Acl_Role_Admin)) {
                    $roles[] = $role->getRoleId();
                }
            }
            $roles = array_values(array_unique($roles));
            $ret->whereEquals('role', $roles);
        }
        $ret->whereEquals('deleted', 0);
        return $ret;
    }

    public function init()
    {
        $this->_model = Zend_Registry::get('userModel');
    }

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column('firstname'));
        $this->_columns->add(new Kwf_Grid_Column('lastname'));
        $this->_columns->add(new Kwf_Grid_Column('role'))
             ->setData(new Kwf_Controller_Action_User_Users_RoleData());
        $this->_columns->add(new Kwf_Grid_Column('email'));
        $this->_columns->add(new Kwf_Grid_Column('locked'));
    }

    public function jsonChangeUserAction()
    {
        if (!$this->_getParam('userId')) {
            throw new Kwf_Exception("userId is required");
        }
        $select = self::_getSelect();
        $select->whereEquals('id', $this->_getParam('userId'));
        $user = $this->_model->getRow($select);
        if (!$user) {
            throw new Kwf_Exception_AccessDenied();
        }
        $this->_getModel()->changeUser($user);
    }
}
