<?php
class Kwc_User_Detail_GeneralCommunity_Avatar_Small_Component extends Kwc_User_Detail_GeneralCommunity_Avatar_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(
            array('width'=>40, 'height'=>40, 'scale'=>Kwf_Media_Image::SCALE_CROP)
        );
        unset($ret['generators']['small']);
        $ret['useParentImage'] = true;
        return $ret;
    }
}
