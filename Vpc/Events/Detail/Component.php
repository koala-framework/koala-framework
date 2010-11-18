<?php
class Vpc_Events_Detail_Component extends Vpc_News_Detail_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assetsAdmin']['dep'][] = 'VpsFormDateTimeField';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = Vpc_Directories_Item_Detail_Component::getTemplateVars();
        $ret['title'] = $this->getData()->row->title;
        $ret['start_date'] = $this->getData()->row->start_date;
        $ret['end_date'] = $this->getData()->row->end_date;
        return $ret;
    }


    public static function modifyItemData(Vps_Component_Data $new)
    {
        Vpc_Directories_Item_Detail_Component::modifyItemData($new);
        $new->start_date = $new->row->start_date;
        $new->end_date = $new->row->end_date;
    }
}
