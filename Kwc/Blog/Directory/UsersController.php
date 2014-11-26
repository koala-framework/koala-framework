<?php
class Kwc_Blog_Directory_UsersController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_defaultOrder = 'lastname';
    protected $_paging = 10;

    protected $_permissions = array();

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

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('deleted', false);
        return $ret;
    }
}
