<?php
class Vps_Controller_Action_User_MailsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_tableName = 'Vps_Dao_UserMails';
    protected $_buttons = array();
    protected $_sortable = true;
    protected $_defaultOrder = 'name';
    protected $_paging = 0;
    protected $_editDialog = array('controllerUrl'=>'/vps/user/mail',
                                   'width'=>600,
                                   'height'=>550);
    protected function _initColumns()
    {
        parent::_initColumns();
        if (Zend_Registry::get('userModel')->getAuthedUserRole() == 'admin') {
            $this->_buttons = array('add' => true, 'delete' => true);
        }

        $this->_columns->add(new Vps_Grid_Column('name', 'Name', 400));
        $this->_columns->add(new Vps_Grid_Column('template', 'Template', 200));
        $this->_columns->add(new Vps_Grid_Column('variable', 'Variable', 200));
        $this->_columns->add(new Vps_Grid_Column_Button('edit'));
    }
}
