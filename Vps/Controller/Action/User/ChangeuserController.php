<?php
class Vps_Controller_Action_User_ChangeuserController extends Vps_Controller_Action_Auto_Grid
{
    protected $_defaultOrder = 'id';

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_table = Zend_Registry::get('userModel');
        $this->_columns->add(new Vps_Auto_Grid_Column('name'));
        $this->_columns->add(new Vps_Auto_Grid_Column('role'))
             ->setData(new Vps_Controller_Action_User_Users_RoleData());
        $this->_columns->add(new Vps_Auto_Grid_Column('email'));
    }

    public function jsonChangeUserAction()
    {
        if (!$this->_getParam('userId')) {
            throw new Vps_Exception("userId is required");
        }
        $storage = Zend_Auth::getInstance()->getStorage();
        $loginData = $storage->read();
        if (!isset($loginData['changeUserId'])) {
            $loginData['changeUserId'] = $loginData['userId'];
        }
        $loginData['userId'] = $this->_getParam('userId');
        $storage->write($loginData);
    }
}
