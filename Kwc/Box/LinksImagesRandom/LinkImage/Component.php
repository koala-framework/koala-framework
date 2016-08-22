<?php
class Kwc_Box_LinksImagesRandom_LinkImage_Component extends Kwc_Composite_LinkImage_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['image'] = 'Kwc_Box_LinksImagesRandom_LinkImage_Image_Component';
        return $ret;
    }
}
