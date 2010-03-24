<?php
abstract class Vpc_News_Detail_Abstract_Component extends Vpc_Directories_Item_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['content'] = 'Vpc_Paragraphs_Component';
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['title'] = $this->getData()->row->title;
        $ret['publish_date'] = $this->getData()->row->publish_date;
        return $ret;
    }


    public static function modifyItemData(Vps_Component_Data $new)
    {
        parent::modifyItemData($new);
        if (isset($new->row->publish_date)) {
            $new->publish_date = $new->row->publish_date;
        }
    }
}
