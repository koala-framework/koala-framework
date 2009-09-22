<?php
class Vpc_Shop_Cart_Checkout_OrdersController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array('add');
    protected $_modelName = 'Vpc_Shop_Cart_Orders';
    protected $_paging = 30;
    protected $_defaultOrder = array('field'=>'order_number', 'direction'=>'DESC');
    protected $_queryFields = array(
        'order_number',
        'customer_number',
        'invoice_number',
        'firstname',
        'lastname',
        'zip',
        'email',
        'city'
    );

    protected function _initColumns()
    {
        $this->_filters = array(
            'text' => true,
            'canceled' => array(
                'type'      => 'Button',
                'skipWhere' => true,
                'cls'       => 'x-btn-text-icon',
                'icon'      => '/assets/silkicons/stop.png',
                'text'      => trlVps('canceled'),
                'tooltip'   => trlVps('Show canceled orders')
            ),
            'shipped' => array(
                'type'      => 'Button',
                'skipWhere' => true,
                'cls'       => 'x-btn-text-icon',
                'icon'      => '/assets/silkicons/package.png',
                'text'      => trlVps('shipped'),
                'tooltip'   => trlVps('Show shipped orders')
            ),
        );

        $this->_columns->add(new Vps_Grid_Column('order_number', trlVps('Order Nr'), 50));
        $this->_columns->add(new Vps_Grid_Column_Date('date', trlVps('Date')));
        $this->_columns->add(new Vps_Grid_Column('firstname', trlVps('Firstname'), 100));
        $this->_columns->add(new Vps_Grid_Column('lastname', trlVps('Lastname'), 100));
        $this->_columns->add(new Vps_Grid_Column('sum_amount', trlVps('Amount'), 30));
        $this->_columns->add(new Vps_Grid_Column('payment', trlVps('Payment'), 100));
        $this->_columns->add(new Vps_Grid_Column_Date('payed', trlVps('Payed')));
        $this->_columns->add(new Vps_Grid_Column_Button('invoice', trlVps('Invoice')))
            ->setButtonIcon('/assets/silkicons/page_white_text.png');
        $this->_columns->add(new Vps_Grid_Column_Button('shipped', trlVps('Shipped')))
            ->setButtonIcon('/assets/silkicons/package_go.png');
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('status', array('ordered', 'payed'));
        if ($this->_getParam('query_canceled')) {
            $ret->whereEquals('canceled', 1);
        } else {
            $ret->whereEquals('canceled', 0);
        }
        if (!$this->_getParam('query_shipped')) {
            $ret->where(new Vps_Model_Select_Expr_IsNull('shipped'));
        } else {
            $ret->where(new Vps_Model_Select_Expr_Not(new Vps_Model_Select_Expr_IsNull('shipped')));
        }
        return $ret;
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
        if (!$order->invoice_date) {
            $order->invoice_date = date('Y-m-d');
            $order->save();
        }
        $pdf = new Vpc_Shop_Cart_Checkout_InvoicePdf($order);
        $pdf->output();
        exit;
    }

    public function jsonShippedAction()
    {
        $id = $this->_getParam('id');
        if (!$id) throw new Vps_Exception("No id given");

        $order = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')->getRow($id);
        $order->shipped = date('Y-m-d');
        $order->save();

        if ($order->getMailEmail()) {
            $checkout = Vps_Component_Data_Root::getInstance()
                ->getComponentById($order->checkout_component_id);
            $mail = $checkout->getChildComponent('-'.$order->payment)
                ->getChildComponent('-shippedMail')
                ->getComponent();
            $data = array(
                'order' => $order,
                'sumRows' => $checkout->getComponent()->getSumRows($order)
            );
            $mail->send($order, $data);
        }
    }
}
