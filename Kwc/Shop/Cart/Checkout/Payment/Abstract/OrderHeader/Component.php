<?php
class Kwc_Shop_Cart_Checkout_Payment_Abstract_OrderHeader_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['order'] = $this->_getOrder();
        $ret['paymentTypeText'] = null;
        $c = $this->getData()->parent->componentClass;
        if (!is_instance_of($c, 'Kwc_Shop_Cart_Checkout_Payment_None_Component')) {
            $ret['paymentTypeText'] = $this->getData()->trlStaticExecute(
                Kwc_Abstract::getSetting($c, 'componentName')
            );
        }
        return $ret;
    }

    protected function _getOrder()
    {
        return Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->getData()->getParentByClass('Kwc_Shop_Cart_Component')->componentClass, 'childModel'))
            ->getReferencedModel('Order')->getCartOrder();
    }
}
