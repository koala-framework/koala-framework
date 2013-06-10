<?php
class Kwc_Blog_Detail_Cc_Component extends Kwc_Directories_Item_Detail_Cc_Component
{
    public static function modifyItemData(Kwf_Component_Data $new)
    {
        parent::modifyItemData($new);
        $new->publish_date = $new->chained->row->publish_date;
    }
}
