<?php
class Vpc_Box_LinksImagesRandom_Component extends Vpc_Abstract_ListRandom_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Vpc_Box_LinksImagesRandom_LinkImage_Component';
        return $ret;
    }
}
