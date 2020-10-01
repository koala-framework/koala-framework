<?php
class Kwc_Articles_Detail_Trl_Component extends Kwc_Directories_Item_Detail_Trl_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['title'] = $this->getData()->row->title;
        return $ret;
    }

    public static function modifyItemData(Kwf_Component_Data $item)
    {
        parent::modifyItemData($item);
        $item->title = $item->row->title;
        $item->teaser = $item->row->teaser;
        $item->date = $item->chained->row->date;
    }
}
