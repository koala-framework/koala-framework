<?php
class Vps_Crm_Customer_CommentsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_defaultOrder = array('field'=>'id', 'direction'=>'DESC');
    protected $_modelName = 'Vps_Crm_Customer_Model_Comments';
    protected $_paging = 6;
    protected $_buttons = array('add', 'delete');

    public function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column_Button('edit'));

        $this->_columns->add(new Vps_Grid_Column('insert_date', trlVps('Date'), 100));
        $this->_columns->add(new Vps_Grid_Column('value', trlVps('Text'), 280));
    }

    protected function _getWhere()
    {
        $where = parent::_getWhere();
        $where['customer_id = ?'] = $this->_getParam('customer_id');
        return $where;
    }
}
