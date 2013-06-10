<?php
class Kwc_Blog_Comments_Detail_Actions_Component extends Kwc_Posts_Detail_Actions_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['quote']['component'] = null;
        $ret['generators']['report']['component'] = null;
        return $ret;
    }
}
