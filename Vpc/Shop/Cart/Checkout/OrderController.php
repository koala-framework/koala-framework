<?php
class Vpc_Shop_Cart_Checkout_OrderController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Vpc_Shop_Cart_Orders';

    protected function _initFields()
    {
        $fs = $this->_form->add(new Vps_Form_Container_FieldSet(trlVps('Order')));
        $fs->add(new Vps_Form_Field_Select('origin', trlVps('Origin')))
            ->setValues(array(
                //TODO: im web einstellbar machen, pool oder so
                'internet' => trlVps('Internet'),
                'phone' => trlVps('Phone'),
                'folder' => trlVps('Folder')
            ))
            ->setAllowBlank(false);

        $cc = Vpc_Abstract::getChildComponentClasses($this->_getParam('class'), 'payment');
        $payments = array();
        foreach ($cc as $k=>$c) {
            $payments[$k] = Vpc_Abstract::getSetting($c, 'componentName');
        }
        $fs->add(new Vps_Form_Field_Select('payment', trlVps('Payment')))
            ->setValues($payments)
            ->setAllowBlank(false);

        $fs->add(new Vps_Form_Field_ShowField('order_number', trlVps('Order Nr')));
        $fs->add(new Vps_Form_Field_ShowField('invoice_number', trlVps('Invoice Nr')));
        $fs->add(new Vps_Form_Field_ShowField('customer_number', trlVps('Customer Nr')));
        $fs->add(new Vps_Form_Field_DateField('invoice_date', trlVps('Invoice Date')));
        $fs->add(new Vps_Form_Field_DateField('payed', trlVps('Payed')));
        $fs->add(new Vps_Form_Field_ShowField('shipped', trlVps('Shipped')))
            ->setTpl('{value:localizedDate}');
        $fs->add(new Vps_Form_Field_Checkbox('canceled', trlVps('Canceled')));

        $fs = $this->_form->add(new Vps_Form_Container_FieldSet(trlVps('Customer')));

        $formComponent = Vpc_Abstract::getChildComponentClass($this->_getParam('class'), 'child', 'form');
        $customerForm = $fs->add(Vpc_Abstract_Form::createComponentForm($formComponent, 'form'));
        $customerForm->setIdTemplate('{0}');
        unset($customerForm->fields['payment']);
        foreach ($customerForm->fields as $f) {
            if ($f->getHideFieldInBackend()) {
                $customerForm->fields->remove($f);
            }
        }
        $customerForm->fields['email']->setAllowBlank(true);
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->status = 'ordered';
        $row->checkout_component_id = $this->_getParam('componentId');
    }
}
