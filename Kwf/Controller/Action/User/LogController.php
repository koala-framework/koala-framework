<?php
class Kwf_Controller_Action_User_LogController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array();
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_model = 'Kwf_User_MessagesModel';
    protected $_paging = 7;

    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Kwf_Grid_Column('message_date', trlKwf('Date'), 110))
            ->setRenderer('localizedDatetime');
        $this->_columns->add(new Kwf_Grid_Column('message', trlKwf('Message'), 400))
            ->setData(new Kwf_Controller_Action_User_Users_LogMessageData());
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('user_id', $this->_getParam('user_id'));
        $ret->whereEquals('create_type', 'auto');
        return $ret;
    }

    protected function _hasPermissions($row, $action)
    {
        $acl = Kwf_Registry::get('acl');
        $userRole = Kwf_Registry::get('userModel')->getAuthedUserRole();

        if (!($acl->getRole($userRole) instanceof Kwf_Acl_Role_Admin)) { //admin always sees all roles
            $roles = array();
            foreach ($acl->getAllowedEditRolesByRole($userRole) as $role) {
                $roles[$role->getRoleId()] = $role->getRoleName();
            }
            if (!$roles) return false;
            if (!array_key_exists($row->getParentRow('User')->role, $roles)) {
                return false;
            }
        }

        return true;
    }
}
