<?php
class Vpc_Box_ImagesRandom_Component extends Vpc_Abstract_ListRandom_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Vpc_Basic_Image_Component';
        return $ret;
    }
}
