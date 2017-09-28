<?php
class Kwf_Controller_Action_User_ChangeuserController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_defaultOrder = 'lastname';
    protected $_paging = 10;

    protected function _isAllowedResource()
    {
        $acl = $this->_getAcl();

        $resource = $this->getRequest()->getResourceName();

        $role = Zend_Registry::get('userModel')->getAuthedChangedUserRole();
        $allowed = $acl->isAllowed($role, $resource, 'view');

        return $allowed;
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $authedChangedRole = Zend_Registry::get('userModel')->getAuthedChangedUserRole();
        $acl = Zend_Registry::get('acl');
        if (!($acl->getRole($authedChangedRole) instanceof Kwf_Acl_Role_Admin)) {
            $roles = array();
            foreach ($acl->getAllowedEditResourceRoleIdsByRole($authedChangedRole) as $roleId) {
                $role = $acl->getRole($roleId);
                if ($role instanceof Kwf_Acl_Role && !($role instanceof Kwf_Acl_Role_Admin)) {
                    $roles[] = $roleId;
                }
            }
            $roles = array_values(array_unique($roles));
            $ret->whereEquals('role', $roles);
        }
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

    public function changeUserAction()
    {
        if (!$this->_getParam('email')) {
            throw new Kwf_Exception_Client("email is required");
        }
        $select = self::_getSelect();
        $select->whereEquals('email', $this->_getParam('email'));
        $user = $this->_model->getRow($select);
        if (!$user) {
            throw new Kwf_Exception_AccessDenied();
        }

        $storage = Kwf_Auth::getInstance()->getStorage();
        $loginData = $storage->read();
        if (!isset($loginData['changeUserId'])) {
            $loginData['changeUserId'] = $loginData['userId'];
        }
        $loginData['userId'] = $user->id;
        $storage->write($loginData);

        header('Location: /');
        exit;
    }
}
