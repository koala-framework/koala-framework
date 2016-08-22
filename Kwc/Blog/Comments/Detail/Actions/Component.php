<?php
class Kwc_Blog_Comments_Detail_Actions_Component extends Kwc_Posts_Detail_Actions_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['quote']['component'] = null;
        $ret['generators']['report']['component'] = null;
        return $ret;
    }
}
