<?php
class Vpc_Forum_FeedList_Feed_Component extends 
{
    protected function _getRssEntryByItem(Vps_Component_Data $item)
    {
        $ret = parent::_getRssEntryByItem($item);
        $ret['description'] = substr(0, max(strlen($item->content), 50), $item->content);
        return $ret;
    }
}
