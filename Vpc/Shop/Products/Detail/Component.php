<?php
class Vpc_Shop_Products_Detail_Component extends Vpc_Directories_Item_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['content'] = 'Vpc_Paragraphs_Component';
        $ret['generators']['child']['component']['image'] = 'Vpc_Basic_Image_Component';
        $ret['generators']['child']['component']['text'] = 'Vpc_Basic_Text_Component';
        $ret['generators']['addToCart'] = array(
            'class' => 'Vpc_Shop_Products_Detail_AddToCartGenerator',
            'component' => array(
                'product' => 'Vpc_Shop_AddToCart_Component'
            ),
            'column' => 'component'
        );
        $ret['cssClass'] = 'webStandard';
        $ret['placeholder']['back'] = trlVps('Back');
        $ret['assetsAdmin']['dep'][] = 'VpsFormDateTimeField';
        return $ret;
    }

    public static function modifyItemData(Vps_Component_Data $item)
    {
        $item->previewImage = $item->getChildComponent('-image');
        $item->previewText = $item->getChildComponent('-text');
    }
}
