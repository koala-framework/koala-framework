<?php
class Vps_Controller_Action_User_LogController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array();
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_model = 'Vps_User_MessagesModel';
    protected $_paging = 7;

    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Vps_Grid_Column('message_date', trlVps('Date'), 110))
            ->setRenderer('localizedDatetime');
        $this->_columns->add(new Vps_Grid_Column('message', trlVps('Message'), 400))
            ->setData(new Vps_Controller_Action_User_Users_LogMessageData());
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('user_id', $this->_getParam('user_id'));
        $ret->whereEquals('create_type', 'auto');
        return $ret;
    }
}
