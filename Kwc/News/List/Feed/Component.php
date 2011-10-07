<?php
class Kwc_News_List_Feed_Component extends Kwc_Directories_List_Feed_Component
{
    protected function _getRssEntryByItem(Kwf_Component_Data $item)
    {
        $ret = parent::_getRssEntryByItem($item);
        $ret['description'] = $item->teaser;
        $ret['lastUpdate'] = strtotime($item->publish_date);
        return $ret;
    }
}
