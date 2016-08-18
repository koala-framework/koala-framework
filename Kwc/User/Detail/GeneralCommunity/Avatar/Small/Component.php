<?php
class Kwc_User_Detail_GeneralCommunity_Avatar_Small_Component extends Kwc_Basic_ImageParent_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['dimension'] = array('width'=>40, 'height'=>40, 'cover' => true);
        return $ret;
    }
}
