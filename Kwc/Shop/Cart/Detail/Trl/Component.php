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

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $addCmp = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->getData()->chained->row->add_component_id, array('subroot'=>$this->getData()));
        if ($addCmp) {
            $data = Kwc_Shop_VoucherProduct_AddToCart_OrderProductData::getInstance($this->getData()->chained->row->add_component_class);
            $ret['product'] = $addCmp->parent->parent;
            $ret['row'] = $this->getData()->chained->row;
            $ret['price'] = $addCmp->getComponent()->getPrice($ret['row']);
            $ret['text'] = $data->getProductTextDynamic($ret['row']);
        }
        return $ret;
    }
}
