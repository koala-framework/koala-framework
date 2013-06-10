<?php
class Kwc_Blog_Detail_Trl_Component extends Kwc_Directories_Item_Detail_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['title'] = $this->getData()->row->title;

        $ret['editComponents'] = array('content');
        return $ret;
    }

    public function hasContent()
    {
        return $this->getData()->getChildComponent('-content')->hasContent();
    }

    public static function modifyItemData(Kwf_Component_Data $new)
    {
        parent::modifyItemData($new);
        $new->publish_date = $new->chained->row->publish_date;
    }
}
