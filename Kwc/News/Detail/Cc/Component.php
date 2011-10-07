<?php
class Vpc_News_Detail_Cc_Component extends Vpc_News_Detail_Abstract_Cc_Component
{
    public static function modifyItemData(Vps_Component_Data $new)
    {
        parent::modifyItemData($new);
        $new->previewImage = $new->getChildComponent('-image');
    }
}
