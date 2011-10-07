<?php
class Vpc_Shop_Cart_Checkout_OrderController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Vpc_Shop_Cart_Orders';

    protected function _initFields()
    {
        $this->_form->add(new Vps_Form_Field_ComboBoxText('origin', trlVps('Origin')))
            ->setValues(array(
                //TODO: im web einstellbar machen, pool oder so
                trlVps('Internet'),
                trlVps('Phone'),
                trlVps('Folder'),
                trlVps('Fair')
            ))
            ->setShowNoSelection(true);

        $cc = Vpc_Abstract::getChildComponentClasses($this->_getParam('class'), 'payment');
        $payments = array();
        foreach ($cc as $k=>$c) {
            $payments[$k] = Vpc_Abstract::getSetting($c, 'componentName');
        }
        $this->_form->add(new Vps_Form_Field_Select('payment', trlVps('Payment')))
            ->setValues($payments)
            ->setAllowBlank(false);

        $cols = $this->_form->add(new Vps_Form_Container_Columns());
        $col = $cols->add();
        $col->add(new Vps_Form_Field_ShowField('order_number', trlVps('Order Nr')));

        $col = $cols->add();
        $col->add(new Vps_Form_Field_ShowField('customer_number', trlVps('Customer Nr')));

        $this->_form->add(new Vps_Form_Field_ShowField('invoice_number', trlVps('Invoice Nr')));

        $this->_form->add(new Vps_Form_Field_DateField('invoice_date', trlVps('Invoice Date')));
        $this->_form->add(new Vps_Form_Field_DateField('payed', trlVps('Payed')));
        $this->_form->add(new Vps_Form_Field_ShowField('shipped', trlVps('Shipped')))
            ->setTpl('{value:localizedDate}');
        $this->_form->add(new Vps_Form_Field_Checkbox('canceled', trlVps('Canceled')));

        $fs = $this->_form->add(new Vps_Form_Container_FieldSet(trlVps('Customer')));


        $formComponent = Vpc_Abstract::getChildComponentClass($this->_getParam('class'), 'child', 'form');
        $formClass = Vpc_Admin::getComponentClass($formComponent, 'FrontendForm');
        $customerForm = new $formClass('form', $formComponent);
        $customerForm->setIdTemplate('{0}');
        $fs->add($customerForm);
        unset($customerForm->fields['payment']);
        foreach ($customerForm->fields as $f) {
            if ($f->getHideFieldInBackend()) {
                $customerForm->fields->remove($f);
            }
        }
        $customerForm->fields['email']->setAllowBlank(true);

        foreach (Vpc_Abstract::getComponentClasses() as $c) {
            $g = Vpc_Abstract::getSetting($c, 'generators');
            if (isset($g['checkout']) && $g['checkout']['component'] == $this->_getParam('class')) {
                foreach (Vpc_Abstract::getSetting($c, 'plugins') as $p) {
                    if (is_instance_of($p, 'Vpc_Shop_Cart_Plugins_Interface')) {
                        $p = new $p();
                        $p->alterBackendOrderForm($this->_form);
                    }
                }
            }
        }
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->status = 'ordered';
        $row->checkout_component_id = $this->_getParam('componentId');
        $row->cart_component_class = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'))
            ->parent->componentClass;
    }
}
