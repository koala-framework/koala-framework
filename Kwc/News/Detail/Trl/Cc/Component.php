<?php
class Kwc_News_Detail_Trl_Cc_Component extends Kwc_News_Detail_Abstract_Trl_Cc_Component
{
    public static function modifyItemData(Kwf_Component_Data $new)
    {
        parent::modifyItemData($new);
        $new->previewImage = $new->getChildComponent('-image');
    }
}
