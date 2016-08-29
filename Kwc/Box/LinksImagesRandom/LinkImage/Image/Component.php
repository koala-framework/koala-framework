<?php
class Kwc_Box_LinksImagesRandom_LinkImage_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['dimensions'] = array(0, 0);
        return $ret;
    }
}
