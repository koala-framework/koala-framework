<?php
class Vpc_Shop_Cart_Plugins_Voucher_VouchersController extends Vps_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Vpc_Shop_Cart_Plugins_Voucher_Vouchers';
    protected $_buttons = array('add', 'delete');
    protected $_paging = 25;
    protected $_filters = array('text'=>true);

    public function indexAction()
    {
        parent::indexAction();
        $cfg = Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig();
        $this->view->assign($cfg['grid']);
        unset($this->view->title);
    }

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column_Date('date', trlVps('Date')));
        $this->_columns->add(new Vps_Grid_Column('code', trlVps('Code')));
        $this->_columns->add(new Vps_Grid_Column('amount', trlcVps('Amount of Money', 'Amount'), 50))
            ->setRenderer('euroMoney');
        $this->_columns->add(new Vps_Grid_Column('used_amount', trlcVps('Amount of Money', 'Used Amount'), 50))
            ->setType('float')
            ->setRenderer('euroMoney');
        $this->_columns->add(new Vps_Grid_Column('comment', trlVps('Comment')))
            ->setRenderer('nl2br');
    }
}
