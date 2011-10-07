<?php
class Vps_Crm_Customer_CustomersController
    extends Vps_Controller_Action_Auto_Grid
{
    protected $_defaultOrder = array('field'=>'name', 'direction'=>'ASC');
    protected $_modelName = 'Vps_Crm_Customer_Model_Customers';
    protected $_paging = 25;
    protected $_buttons = array('add', 'delete');
    protected $_filters = array('text' => true);

    public function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column('name', trlVps('Name'), 190));
        $this->_columns->add(new Vps_Grid_Column('street', trlVps('Street'), 150))
            ->setHidden(true);
        $this->_columns->add(new Vps_Grid_Column('zip', trlVps('ZIP'), 55));
        $this->_columns->add(new Vps_Grid_Column('city', trlVps('City'), 120));
        $this->_columns->add(new Vps_Grid_Column('phone', trlVps('Phone'), 120))
            ->setHidden(true);
        $this->_columns->add(new Vps_Grid_Column('fax', trlVps('Fax'), 120))
            ->setHidden(true);
        $this->_columns->add(new Vps_Grid_Column('email', trlVps('E-Mail'), 120))
            ->setHidden(true);
        $this->_columns->add(new Vps_Grid_Column('website', trlVps('Website'), 120))
            ->setHidden(true);
    }
}
