<?php
class Kwc_Shop_Cart_Checkout_OrderController extends Kwf_Controller_Action_Auto_Form
{
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');

    protected function _initFields()
    {
        $this->_form->setModel(Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting(Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->_getParam('componentId'))->parent->componentClass, 'childModel'))
            ->getReferencedModel('Order'));

        $this->_form->add(new Kwf_Form_Field_ComboBoxText('origin', trlKwf('Origin')))
            ->setValues(array(
                //TODO: im web einstellbar machen, pool oder so
                trlKwf('Internet'),
                trlKwf('Phone'),
                trlKwf('Folder'),
                trlKwf('Fair')
            ))
            ->setShowNoSelection(true);

        $cc = Kwc_Abstract::getChildComponentClasses($this->_getParam('class'), 'payment');
        $payments = array();
        foreach ($cc as $k=>$c) {
            $payments[$k] = Kwc_Abstract::getSetting($c, 'componentName');
        }
        if (count($payments) > 1) {
            $this->_form->add(new Kwf_Form_Field_Select('payment', trlKwf('Payment')))
                ->setValues($payments)
                ->setAllowBlank(false);
        }

        $cols = $this->_form->add(new Kwf_Form_Container_Columns());
        $col = $cols->add();
        $col->add(new Kwf_Form_Field_ShowField('order_number', trlKwf('Order Nr')));

        $col = $cols->add();
        $col->add(new Kwf_Form_Field_ShowField('customer_number', trlKwf('Customer Nr')));

        if (Kwc_Abstract::getSetting($this->_getParam('class'), 'generateInvoices')) {
            $this->_form->add(new Kwf_Form_Field_ShowField('invoice_number', trlKwf('Invoice Nr')));
            $this->_form->add(new Kwf_Form_Field_DateField('invoice_date', trlKwf('Invoice Date')));
        }

        $this->_form->add(new Kwf_Form_Field_DateField('payed', trlKwf('Payed')));
        $this->_form->add(new Kwf_Form_Field_ShowField('shipped', trlKwf('Shipped')))
            ->setTpl('{value:localizedDate}');
        $this->_form->add(new Kwf_Form_Field_Checkbox('canceled', trlKwf('Canceled')));

        $fs = $this->_form->add(new Kwf_Form_Container_FieldSet(trlKwf('Customer')));


        $formComponent = Kwc_Abstract::getChildComponentClass($this->_getParam('class'), 'child', 'form');
        $formClass = Kwc_Admin::getComponentClass($formComponent, 'FrontendForm');
        $customerForm = new $formClass('form', $formComponent);
        $customerForm->setIdTemplate('{0}');
        $customerForm->setModel($this->_form->getModel());
        $fs->add($customerForm);
        unset($customerForm->fields['payment']);
        foreach ($customerForm->fields as $f) {
            if ($f->getHideFieldInBackend()) {
                $customerForm->fields->remove($f);
            }
        }
        $customerForm->fields['email']->setAllowBlank(true);
        $customerForm->setAllowEmptyCart(true);

        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            $g = Kwc_Abstract::getSetting($c, 'generators');
            if (isset($g['checkout']) && $g['checkout']['component']['checkout'] == $this->_getParam('class')) {
                foreach (Kwc_Abstract::getSetting($c, 'plugins') as $p) {
                    if (is_instance_of($p, 'Kwc_Shop_Cart_Plugins_Interface')) {
                        $p = new $p();
                        $p->alterBackendOrderForm($this->_form);
                    }
                }
            }
        }
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->status = 'ordered';
        $row->checkout_component_id = $this->_getParam('componentId');
        $row->cart_component_class = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'))
            ->parent->componentClass;
    }
}
