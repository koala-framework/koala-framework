<?php
class Vpc_Forum_FeedList_Feed_Component extends Vpc_Directories_List_Feed_Component
{
    protected function _getRssEntryByItem(Vps_Component_Data $item)
    {
        $ret = parent::_getRssEntryByItem($item);
        $ret['description'] = substr(0, max(strlen($item->row->content), 50), $item->row->content);
        $ret['lastUpdate'] = strtotime($item->row->create_time);
        return $ret;
    }
}
