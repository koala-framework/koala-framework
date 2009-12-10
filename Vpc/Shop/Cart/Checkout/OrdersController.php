<?php
class Vpc_Shop_Cart_Checkout_OrdersController_Payment extends Vps_Data_Abstract
{
    private $_payments;
    public function __construct($payments)
    {
        $this->_payments = $payments;
    }
    public function load($row)
    {
        if (!isset($this->_payments[$row->payment])) return $row->payment;
        return $this->_payments[$row->payment];
    }
}
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
        $cc = Vpc_Abstract::getChildComponentClasses($this->_getParam('class'), 'payment');
        $paymentsFilterData = array();
        $payments = array();
        foreach ($cc as $k=>$c) {
            $payments[$k] = Vpc_Abstract::getSetting($c, 'componentName');
            $paymentsFilterData[] = array($k, $payments[$k]);
        }

        $this->_filters = array(
            'text' => true,
            'payment' => array(
                'type'   => 'ComboBox',
                'text'   => trlVps('Payment'),
                'data'   => $paymentsFilterData,
                'width'  => 100
            ),
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
        $this->_columns->add(new Vps_Grid_Column('invoice_number', trlVps('Invoice Nr'), 50))
            ->setHidden(true);
        $this->_columns->add(new Vps_Grid_Column_Date('date', trlVps('Date')));
        $this->_columns->add(new Vps_Grid_Column('firstname', trlVps('Firstname'), 100));
        $this->_columns->add(new Vps_Grid_Column('lastname', trlVps('Lastname'), 100));
        $this->_columns->add(new Vps_Grid_Column('sum_amount', trlVps('Amt'), 30));
        $this->_columns->add(new Vps_Grid_Column('payment', trlVps('Payment'), 100))
            ->setData(new Vpc_Shop_Cart_Checkout_OrdersController_Payment($payments));
        $this->_columns->add(new Vps_Grid_Column_Date('payed', trlVps('Payed')));
        $this->_columns->add(new Vps_Grid_Column_Button('invoice', trlcVps('Invoice', 'IN')));
        $this->_columns->add(new Vps_Grid_Column_Button('shipped', trlcVps('Shipped', 'SH')))
            ->setButtonIcon('/assets/silkicons/package_go.png');

        $this->_columns->add(new Vps_Grid_Column('shipped'));
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
        }
        if (!$order->invoice_number) {
            $s = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')->select();
            $s->limit(1);
            $s->order('invoice_number', 'DESC');
            $row = Vps_Model_Abstract::getInstance('Vpc_Shop_Cart_Orders')->getRow($s);
            $maxNumber = 0;
            if ($row) $maxNumber = $row->invoice_number;
            $order->invoice_number = $maxNumber + 1;
        }
        $order->save();

        $cls = Vpc_Admin::getComponentClass($this->_getParam('class'), 'InvoicePdf');
        $pdf = new $cls($order);
        Vps_Media_Output::output(array(
            'contents' => $pdf->output('', 'S'),
            'mimeType' => 'application/pdf',
            'downloadFilename' => $order->order_number.'.pdf'
        ));
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
