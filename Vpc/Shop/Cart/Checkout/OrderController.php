<?php
class Vpc_Shop_Cart_Checkout_OrderController extends Vps_Controller_Action_Auto_Form
{
    protected $_buttons = array('save');
    protected $_modelName = 'Vpc_Shop_Cart_Orders';

    protected function _initFields()
    {
        $fs = $this->_form->add(new Vps_Form_Container_FieldSet(trlVps('Order')));
        $fs->add(new Vps_Form_Field_DateField('package_sent', trlVps('Package Sent')));
        $fs->add(new Vps_Form_Field_DateField('payed', trlVps('Payed')));

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
    }
}
