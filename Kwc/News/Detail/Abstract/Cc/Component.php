<?php
class Vpc_News_Detail_Abstract_Cc_Component extends Vpc_Directories_Item_Detail_Cc_Component
{
    public static function modifyItemData(Vps_Component_Data $new)
    {
        parent::modifyItemData($new);
        $new->publish_date = $new->chained->row->publish_date;
        $new->teaser = $new->chained->row->teaser;
    }
}
