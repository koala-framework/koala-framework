<?php
class Vps_Controller_Action_User_CommentsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array('add', 'save');
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_model = 'Vps_User_MessagesModel';
    protected $_paging = 6;

    protected function _initColumns()
    {
        parent::_initColumns();

        $this->_columns->add(new Vps_Grid_Column('message_date', trlVps('Date'), 110))
            ->setRenderer('localizedDatetime');
        $this->_columns->add(new Vps_Grid_Column('message', trlVps('Comment'), 450))
            ->setEditor(new Vps_Form_Field_TextField());
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('user_id', $this->_getParam('user_id'));
        $ret->whereEquals('create_type', 'manual');
        return $ret;
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row, $submitRow)
    {
        parent::_beforeSave($row, $submitRow);
        $row->user_id = $this->_getParam('user_id');
        $row->create_type = 'manual';
    }
}
