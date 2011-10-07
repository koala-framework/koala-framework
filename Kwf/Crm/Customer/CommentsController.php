<?php
class Kwf_Crm_Customer_CommentsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_defaultOrder = array('field'=>'id', 'direction'=>'DESC');
    protected $_modelName = 'Kwf_Crm_Customer_Model_Comments';
    protected $_paging = 10;
    protected $_buttons = array('add', 'delete');

    public function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));

        $this->_columns->add(new Kwf_Grid_Column('insert_date', trlKwf('Date'), 120));
        $this->_columns->add(new Kwf_Form_Field_ShowField('insert_uid', trlKwf('User')))
            ->setData(new Kwf_Data_Table_Parent('InsertUser'))
            ->setHidden(true);
        $this->_columns->add(new Kwf_Grid_Column('value', trlKwf('Text'), 260));
    }

    protected function _getWhere()
    {
        $where = parent::_getWhere();
        $where['customer_id = ?'] = $this->_getParam('customer_id');
        return $where;
    }
}
