<?php
class Kwc_Blog_Comments_Detail_Component extends Kwc_Posts_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['actions'] = 'Kwc_Blog_Comments_Detail_Actions_Component';
        return $ret;
    }
}
