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

        $this->_columns->add(new Vps_Grid_Column('insert_date', trlVps('Date'), 120));
        $this->_columns->add(new Vps_Form_Field_ShowField('insert_uid', trlVps('User')))
            ->setData(new Vps_Data_Table_Parent('InsertUser'))
            ->setHidden(true);
        $this->_columns->add(new Vps_Grid_Column('value', trlVps('Text'), 260));
    }

    protected function _getWhere()
    {
        $where = parent::_getWhere();
        $where['customer_id = ?'] = $this->_getParam('customer_id');
        return $where;
    }
}
