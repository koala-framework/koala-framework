<?php
class Vpc_Box_LinksImagesRandom_LinkImage_Component extends Vpc_Composite_LinkImage_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['image'] = 'Vpc_Box_LinksImagesRandom_LinkImage_Image_Component';
        return $ret;
    }
}
