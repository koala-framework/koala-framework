<?php
class Kwc_Blog_Comments_Detail_Component extends Kwc_Posts_Detail_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['actions'] = 'Kwc_Blog_Comments_Detail_Actions_Component';
        return $ret;
    }
}
