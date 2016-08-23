<?php
class Kwc_Shop_Products_Directory_Controller extends Kwc_Directories_Item_Directory_Controller
{
    protected $_hasComponentId = false;

    protected $_buttons = array('add', 'delete', 'save');
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column_Visible());
        $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title'), 200));
        $this->_columns->add(new Kwf_Grid_Column('current_price', trlKwf('Current Price'), 100))
            ->setRenderer('euroMoney');
        parent::_initColumns();
    }
}
