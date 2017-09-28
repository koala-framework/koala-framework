<?php
class Kwc_Shop_Cart_Checkout_OrdersController_Payment extends Kwf_Data_Abstract
{
    private $_payments;
    public function __construct($payments)
    {
        $this->_payments = $payments;
    }
    public function load($row, array $info = array())
    {
        if (!isset($this->_payments[$row->payment])) return $row->payment;
        return $this->_payments[$row->payment];
    }
}
class Kwc_Shop_Cart_Checkout_OrdersController_SumAmount extends Kwf_Data_Abstract
{
    public function load($row, array $info = array())
    {
        $ret = 0;
        foreach ($row->getChildRows('Products') as $p) {
            $data = Kwc_Shop_AddToCartAbstract_OrderProductData::getInstance($p->add_component_class);
            $ret += $data->getAmount($p);
        }
        return $ret;
    }
}
class Kwc_Shop_Cart_Checkout_OrdersController_SumPrice extends Kwf_Data_Abstract
{
    public function load($row, array $info = array())
    {
        return $row->getTotal();
    }
}
class Kwc_Shop_Cart_Checkout_OrdersController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array('add');
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
        $this->_model = Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting(Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'))->parent->componentClass, 'childModel'))
            ->getReferencedModel('Order');

        $cc = Kwc_Abstract::getChildComponentClasses($this->_getParam('class'), 'payment');
        $paymentsFilterData = array();
        $payments = array();
        foreach ($cc as $k=>$c) {
            $payments[$k] = Kwf_Trl::getInstance()->trlStaticExecute(
                Kwc_Abstract::getSetting($c, 'componentName')
            );
            $paymentsFilterData[] = array($k, $payments[$k]);
        }
        $this->_filters['text'] = true;
        if (count($payments) > 1) {
            $this->_filters['payment'] = array(
                'type'   => 'ComboBox',
                'text'   => trlKwf('Payment'),
                'data'   => $paymentsFilterData,
                'width'  => 100
            );
        }
        $this->_filters['canceled'] = array(
            'type'      => 'Button',
            'skipWhere' => true,
            'cls'       => 'x2-btn-text-icon',
            'icon'      => '/assets/silkicons/stop.png',
            'text'      => trlKwf('canceled'),
            'tooltip'   => trlKwf('Show canceled orders')
        );
        $this->_filters['shipped'] = array(
            'type'      => 'Button',
            'skipWhere' => true,
            'cls'       => 'x2-btn-text-icon',
            'icon'      => '/assets/silkicons/package.png',
            'text'      => trlKwf('shipped'),
            'tooltip'   => trlKwf('Show shipped orders')
        );

        $this->_columns->add(new Kwf_Grid_Column('order_number', trlKwf('Order Nr'), 50));
        $this->_columns->add(new Kwf_Grid_Column('invoice_number', trlKwf('Invoice Nr'), 50))
            ->setHidden(true);
        $this->_columns->add(new Kwf_Grid_Column_Datetime('date', trlKwf('Date')));
        $this->_columns->add(new Kwf_Grid_Column('firstname', trlKwf('Firstname'), 90));
        $this->_columns->add(new Kwf_Grid_Column('lastname', trlKwf('Lastname'), 90));
        $this->_columns->add(new Kwf_Grid_Column('country', trlKwf('Country'), 15)); // TODO: Pfusch
        $this->_columns->add(new Kwf_Grid_Column('sum_amount', trlKwf('Amt'), 30))
            ->setData(new Kwc_Shop_Cart_Checkout_OrdersController_SumAmount())
            ->setSortable(false);
        $this->_columns->add(new Kwf_Grid_Column('sum_price', trlKwf('Sum'), 50))
            ->setData(new Kwc_Shop_Cart_Checkout_OrdersController_SumPrice())
            ->setSortable(false)
            ->setRenderer('euroMoney');
        if (count($payments) > 1) {
            $this->_columns->add(new Kwf_Grid_Column('payment', trlKwf('Payment'), 80))
                ->setData(new Kwc_Shop_Cart_Checkout_OrdersController_Payment($payments))
                ->setSortable(false);
        }
        $this->_columns->add(new Kwf_Grid_Column_Date('payed', trlKwf('Payed')));
        if (Kwc_Abstract::getSetting($this->_getParam('class'), 'generateInvoices')) {
            $this->_columns->add(new Kwf_Grid_Column_Button('invoice', trlcKwf('Invoice', 'IN')));
        }
        $this->_columns->add(new Kwf_Grid_Column_Button('shipped', trlcKwf('Shipped', 'SH')))
            ->setButtonIcon('/assets/silkicons/package_go.png');

        $this->_columns->add(new Kwf_Grid_Column('shipped'));
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
            $ret->where(new Kwf_Model_Select_Expr_IsNull('shipped'));
        } else {
            $ret->where(new Kwf_Model_Select_Expr_Not(new Kwf_Model_Select_Expr_IsNull('shipped')));
        }
        $ret->whereEquals('checkout_component_id', $this->_getParam('componentId'));
        return $ret;
    }

    public function indexAction()
    {
        $a = Kwc_Admin::getInstance($this->_getParam('class'));

        $this->view->ordersControllerUrl = $a->getControllerUrl('Orders');
        $this->view->orderControllerUrl = $a->getControllerUrl('Order');
        $this->view->orderProductsControllerUrl = $a->getControllerUrl('OrderProducts');
        $this->view->baseParams = array(
            'componentId' => $this->_getParam('componentId')
        );

        $this->view->xtype = 'kwc.shop.cart.checkout.orders';
    }

    public function pdfAction()
    {
        $id = $this->_getParam('id');
        if (!$id) throw new Kwf_Exception("No id given");
        $order = $this->_model->getRow($id);
        if (!$order->invoice_date) {
            $order->invoice_date = date('Y-m-d');
        }
        if (!$order->invoice_number) {
            $order->generateInvoiceNumber();
        }
        $order->save();

        $cls = Kwc_Admin::getComponentClass($this->_getParam('class'), 'InvoicePdf');
        $pdf = new $cls($order);
        Kwf_Media_Output::output(array(
            'contents' => $pdf->output('', 'S'),
            'mimeType' => 'application/pdf',
            'downloadFilename' => $order->order_number.'.pdf'
        ));
    }

    public function jsonShippedAction()
    {
        $id = $this->_getParam('id');
        if (!$id) throw new Kwf_Exception("No id given");

        $order = $this->_model->getRow($id);
        $order->shipped = date('Y-m-d');
        $order->save();

        if ($order->getMailEmail()) {
            $checkout = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($order->checkout_component_id);
            if (!$checkout) throw new Kwf_Exception("Can't find checkout component");
            $mail = $checkout->getChildComponent('-'.$order->payment)
                ->getChildComponent('-shippedMail');
            if ($mail) { //mail is optional
                $data = array(
                    'order' => $order,
                    'sumRows' => $checkout->getComponent()->getSumRows($order)
                );
                $mail->getComponent()->send($order, $data);
            }
        }
    }
}
