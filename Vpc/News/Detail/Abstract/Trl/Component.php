<?php
class Vpc_News_Detail_Abstract_Trl_Component extends Vpc_Directories_Item_Detail_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['title'] = $this->getData()->row->title;
        return $ret;
    }

    public static function modifyItemData(Vps_Component_Data $new)
    {
        parent::modifyItemData($new);
        $new->publish_date = $new->chained->row->publish_date;
        $new->teaser = $new->row->teaser;
    }

}
