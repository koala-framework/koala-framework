<?php
class Kwc_Box_LinksImagesRandom_LinkImage_Image_Component extends Kwc_Basic_Image_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dimensions'] = array(0, 0);
        return $ret;
    }
}
