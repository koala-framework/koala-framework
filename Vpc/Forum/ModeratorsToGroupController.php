<?php
class Vpc_Forum_ModeratorsToGroupController extends Vps_Controller_Action_Auto_AssignGrid
{
    protected $_buttons = array('delete' => true);

    protected $_filters = array();
    protected $_tableName = 'Vpc_Forum_ModeratorModel';
    protected $_assignFromReference = 'User';
    protected $_assignToReference = 'Group';

    protected function _initColumns()
    {
        $serviceUserClass = get_class(Zend_Registry::get('userModel'));

        $this->_columns->add(new Vps_Auto_Grid_Column('user_id', 'ID', 55));
        $this->_columns->add(new Vps_Auto_Grid_Column('title', 'Titel', 60))
                       ->setData(new Vps_Auto_Data_Table_Parent($serviceUserClass, 'title'));
        $this->_columns->add(new Vps_Auto_Grid_Column('firstname', 'Vorname', 120))
                       ->setData(new Vps_Auto_Data_Table_Parent($serviceUserClass, 'firstname'));
        $this->_columns->add(new Vps_Auto_Grid_Column('lastname', 'Zuname', 120))
                       ->setData(new Vps_Auto_Data_Table_Parent($serviceUserClass, 'lastname'));
        $this->_columns->add(new Vps_Auto_Grid_Column('email', 'Email', 180))
                       ->setData(new Vps_Auto_Data_Table_Parent($serviceUserClass, 'email'));
    }

    protected function _getWhere()
    {
        $where = parent::_getWhere();
        if ($this->_getParam('group_id')) {
            $where['group_id = ?'] = $this->_getParam('group_id');
        }
        return $where;
    }
}