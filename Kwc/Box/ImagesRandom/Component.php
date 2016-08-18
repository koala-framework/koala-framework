<?php
class Kwc_Box_ImagesRandom_Component extends Kwc_Abstract_ListRandom_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component'] = 'Kwc_Basic_Image_Component';
        return $ret;
    }
}
