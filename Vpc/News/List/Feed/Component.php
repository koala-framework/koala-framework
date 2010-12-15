<?php
class Vpc_News_List_Feed_Component extends Vpc_Directories_List_Feed_Component
{
    protected function _getRssEntryByItem(Vps_Component_Data $item)
    {
        $ret = parent::_getRssEntryByItem($item);
        $ret['description'] = $item->teaser;
        $ret['lastUpdate'] = strtotime($item->publish_date);
        return $ret;
    }
}
