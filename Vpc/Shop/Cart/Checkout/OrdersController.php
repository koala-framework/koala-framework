<?php
class Vpc_Shop_Cart_Checkout_OrdersController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array();
    protected $_modelName = 'Vpc_Shop_Cart_Orders';
    protected $_paging = 30;

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column_Date('date', trlVps('Date')));
        $this->_columns->add(new Vps_Grid_Column('firstname', trlVps('Firstname'), 100));
        $this->_columns->add(new Vps_Grid_Column('lastname', trlVps('Lastname'), 100));
        $this->_columns->add(new Vps_Grid_Column('status', trlVps('Status'), 50));
        $this->_columns->add(new Vps_Grid_Column('sum_amount', trlVps('Amount'), 30));
        $this->_columns->add(new Vps_Grid_Column('payment', trlVps('Payment'), 100));
        $this->_columns->add(new Vps_Grid_Column_Date('package_sent', trlVps('Sent')));
        $this->_columns->add(new Vps_Grid_Column_Date('payed', trlVps('Payed')));
        $this->_columns->add(new Vps_Grid_Column_Button('invoice', trlVps('Invoice')))
            ->setButtonIcon('/assets/silkicons/page_white_text.png');
        $this->_columns->add(new Vps_Grid_Column_Button('mail', trlVps('Mail')))
            ->setButtonIcon('/assets/silkicons/email.png');
    }

    public function indexAction()
    {
        $a = Vpc_Admin::getInstance($this->_getParam('class'));

        $this->view->ordersControllerUrl = $a->getControllerUrl('Orders');
        $this->view->orderControllerUrl = $a->getControllerUrl('Order');
        $this->view->orderProductsControllerUrl = $a->getControllerUrl('OrderProducts');
        $this->view->baseParams = array(
            'componentId' => $this->_getParam('componentId')
        );

        $this->view->xtype = 'vpc.shop.cart.checkout.orders';
    }

    public function pdfAction()
    {
        $id = $this->_getParam('id');
        if (!$id) throw new Vps_Exception("No id given");
        $order = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')->getRow($id);
        $pdf = new Vpc_Shop_Cart_Checkout_InvoicePdf($order);
        $pdf->output();
        exit;
    }
}
