<?php
class Kwf_Crm_Customer_CustomersController
    extends Kwf_Controller_Action_Auto_Grid
{
    protected $_defaultOrder = array('field'=>'name', 'direction'=>'ASC');
    protected $_modelName = 'Kwf_Crm_Customer_Model_Customers';
    protected $_paging = 25;
    protected $_buttons = array('add', 'delete');
    protected $_filters = array('text' => true);

    public function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Name'), 190));
        $this->_columns->add(new Kwf_Grid_Column('street', trlKwf('Street'), 150))
            ->setHidden(true);
        $this->_columns->add(new Kwf_Grid_Column('zip', trlKwf('ZIP'), 55));
        $this->_columns->add(new Kwf_Grid_Column('city', trlKwf('City'), 120));
        $this->_columns->add(new Kwf_Grid_Column('phone', trlKwf('Phone'), 120))
            ->setHidden(true);
        $this->_columns->add(new Kwf_Grid_Column('fax', trlKwf('Fax'), 120))
            ->setHidden(true);
        $this->_columns->add(new Kwf_Grid_Column('email', trlKwf('E-Mail'), 120))
            ->setHidden(true);
        $this->_columns->add(new Kwf_Grid_Column('website', trlKwf('Website'), 120))
            ->setHidden(true);
    }
}
