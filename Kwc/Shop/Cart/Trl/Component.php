<?php
class Kwc_Shop_Cart_Trl_Component extends Kwc_Directories_Item_Directory_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        return $ret;
    }

    public function preProcessInput()
    {
        // to remove deleted products from the cart
        Kwf_Model_Abstract::getInstance(Kwc_Abstract::getSetting($this->getData()->chained->componentClass, 'childModel'))
            ->getReferencedModel('Order')
            ->getCartOrder()
            ->getProductsDataWithProduct($this->getData());
    }

    public function getOrderProductsModel()
    {
        return $this->getData()->chained->getComponent()->getChildModel();
    }

    public function getFormComponents()
    {
        $ret = array();
        foreach ($this->getData()->getChildComponents(array('generator'=>'detail')) as $c) {
            $ret[] = $c->getChildComponent('-form')
                ->getChildComponent('-child')
                ->getComponent();
        }
        return $ret;
    }

    public function getForms()
    {
        $ret = array();
        foreach ($this->getData()->getChildComponents(array('generator'=>'detail')) as $c) {
            $ret[] = $c->getChildComponent('-form')
                ->getChildComponent('-child')
                ->getComponent()->getForm();
        }
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['checkout'] = $this->getData()->getChildComponent('_checkout');
        $ret['shop'] = $this->getData()->getParentPage();
        return $ret;
    }
}
