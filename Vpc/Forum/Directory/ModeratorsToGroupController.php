<?php
class Vpc_Forum_Directory_ModeratorsToGroupController extends Vps_Controller_Action_Auto_AssignGrid
{
    protected $_buttons = array('delete' => true);

    protected $_filters = array();
    protected $_modelName = 'Vpc_Forum_Group_ModeratorsModel';
    protected $_assignFromReference = 'User';
    protected $_assignToReference = 'Group';

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('user_id', 'ID', 55));
        $this->_columns->add(new Vps_Grid_Column('title', trlVps('Title'), 60))
                       ->setData(new Vps_Data_Table_Parent('User', 'title'));
        $this->_columns->add(new Vps_Grid_Column('firstname', trlVps('Firstname'), 120))
                       ->setData(new Vps_Data_Table_Parent('User', 'firstname'));
        $this->_columns->add(new Vps_Grid_Column('lastname', trlVps('Lastname'), 120))
                       ->setData(new Vps_Data_Table_Parent('User', 'lastname'));
        $this->_columns->add(new Vps_Grid_Column('email', trlVps('Email'), 180))
                       ->setData(new Vps_Data_Table_Parent('User', 'email'));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        if ($this->_getParam('group_id')) {
            $ret->whereEquals('group_id', $this->_getParam('group_id'));
        }
        return $ret;
    }
}