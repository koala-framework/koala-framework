<?php
class Vps_Controller_Action_User_MailsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_tableName = 'Vps_Dao_UserMails';
    protected $_buttons = array('add', 'delete');
    protected $_sortable = true;
    protected $_defaultOrder = 'name';
    protected $_paging = 0;
    protected $_editDialog = array('controllerUrl'=>'/vps/user/mail',
                                   'width'=>600,
                                   'height'=>550);
    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Vps_Auto_Grid_Column('name', 'Name', 400));
        $this->_columns->add(new Vps_Auto_Grid_Column('template', 'Template', 200));
        $this->_columns->add(new Vps_Auto_Grid_Column('variable', 'Variable', 200));
        $this->_columns->add(new Vps_Auto_Grid_Column_Button('edit'));
    }
}
