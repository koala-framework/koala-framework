<?php
class Kwc_Shop_Products_Detail_Trl_Component extends Kwc_Directories_Item_Detail_Trl_Component
{
    public static function modifyItemData(Kwf_Component_Data $item)
    {
        parent::modifyItemData($item);
        $item->previewImage = $item->getChildComponent('-image');
        $item->previewText = $item->getChildComponent('-text');
        $item->currentPrice = $item->chained->row->current_price;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['currentPrice'] = $this->getData()->chained->row->current_price;
        $ret['addToCart'] = $this->getData()->getChildComponent('-addToCart');
        return $ret;
    }

    public function getAddToCartForm()
    {
        return $this->getData()->getChildComponent('-addToCart')->getChildComponent('-form');
    }
}
