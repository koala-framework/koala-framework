<?php
class Vpc_Events_Detail_Trl_Cc_Component extends Vpc_Directories_Item_Detail_Trl_Cc_Component
{
    public static function modifyItemData(Vps_Component_Data $new)
    {
        parent::modifyItemData($new);
        $new->start_date = $new->chained->chained->row->start_date;
        $new->end_date = $new->chained->chained->row->end_date;
    }
}
