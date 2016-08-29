<?php
class Kwc_Box_LinksImagesRandom_Component extends Kwc_Abstract_ListRandom_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component'] = 'Kwc_Box_LinksImagesRandom_LinkImage_Component';
        return $ret;
    }
}
