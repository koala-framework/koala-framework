<?php
class Kwc_News_Detail_Abstract_Trl_Cc_Component extends Kwc_Directories_Item_Detail_Trl_Cc_Component
{
    public static function modifyItemData(Kwf_Component_Data $new)
    {
        parent::modifyItemData($new);
        $new->publish_date = $new->chained->chained->row->publish_date;
    }
}
