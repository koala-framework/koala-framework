<?php
class Kwf_Controller_Action_User_CommentsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array('add', 'save');
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_model = 'Kwf_User_MessagesModel';
    protected $_paging = 6;

    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Kwf_Grid_Column('message_date', trlKwf('Date'), 110))
            ->setRenderer('localizedDatetime');
        $this->_columns->add(new Kwf_Grid_Column('message', trlKwf('Comment'), 450))
            ->setEditor(new Kwf_Form_Field_TextField());
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('user_id', $this->_getParam('user_id'));
        $ret->whereEquals('create_type', 'manual');
        return $ret;
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row, $submitRow)
    {
        parent::_beforeSave($row, $submitRow);
        $row->user_id = $this->_getParam('user_id');
        $row->create_type = 'manual';
    }

    protected function _hasPermissions($row, $action)
    {
        $userId = $this->_getParam('user_id');
        if (!$userId) {
            return false;
        }

        $acl = Kwf_Registry::get('acl');
        $userRole = Kwf_Registry::get('userModel')->getAuthedUserRole();

        $roles = array();
        foreach ($acl->getAllowedEditRolesByRole($userRole) as $role) {
            $roles[$role->getRoleId()] = $role->getRoleName();
        }
        if (!$roles) return false;

        $userModel = Kwf_Registry::get('userModel');
        $userRow = $userModel->getRow($userId);

        if (!$userRow || !array_key_exists($userRow->role, $roles)) {
            return false;
        }

        return true;
    }
}
