<?php
class Kwc_Shop_Products_Detail_RelatedProducts_Product_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Related Products');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['product'] = Kwf_Component_Data_Root::getInstance()->getComponentByClass(
            'Kwc_Shop_Products_Detail_Component',
            array('id' => $this->getRow()->product_id)
        );
        return $ret;
    }
}
