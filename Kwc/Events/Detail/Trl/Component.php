<?php
class Kwc_Events_Detail_Trl_Component extends Kwc_News_Detail_Abstract_Trl_Component
{
    public static function modifyItemData(Kwf_Component_Data $new)
    {
        Kwc_Directories_Item_Detail_Trl_Component::modifyItemData($new);
        $new->start_date = $new->chained->row->start_date;
        $new->end_date = $new->chained->row->end_date;
    }
}
