<?php
class Vps_Crm_Customer_ContactpersonsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_defaultOrder = array('field'=>'lastname', 'direction'=>'ASC');
    protected $_modelName = 'Vps_Crm_Customer_Model_Contactpersons';
    protected $_buttons = array('add', 'delete');
    protected $_permissions = array('add', 'delete');

    public function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column_Button('edit'));

        $this->_columns->add(new Vps_Grid_Column('firstname', trlVps('Firstname'), 100));
        $this->_columns->add(new Vps_Grid_Column('lastname', trlVps('Lastname'), 100));
        $this->_columns->add(new Vps_Grid_Column('phone', trlVps('Phone'), 100));
        $this->_columns->add(new Vps_Grid_Column('email', trlVps('E-Mail'), 100));
    }

    protected function _getWhere()
    {
        $where = parent::_getWhere();
        $where['customer_id = ?'] = $this->_getParam('customer_id');
        return $where;
    }
}
