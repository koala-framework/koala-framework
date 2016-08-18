<?php
class Kwc_Shop_Cart_Detail_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    public function preProcessInput($data)
    {
        if (isset($data[$this->getData()->componentId.'-delete'])) {
            $this->getData()->chained->row->delete();
        }
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $addCmp = Kwc_Shop_AddToCartAbstract_OrderProductData::getAddComponentByDbId(
            $this->getData()->chained->row->add_component_id, $this->getData()
        );
        if ($addCmp) {
            $ret['product'] = $addCmp->getComponent()->getProduct();
            $ret['row'] = $this->getData()->chained->row;
            $ret['price'] = $addCmp->getComponent()->getPrice($ret['row']);
            $ret['text'] = $addCmp->getComponent()->getProductText($ret['row']);
        }
        return $ret;
    }
}
