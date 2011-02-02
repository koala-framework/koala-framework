<?php
class Vpc_List_Gallery_Image_LinkTag_Component extends Vpc_TextImage_ImageEnlarge_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_List_Gallery_Image_LinkTag_Model';
        return $ret;
    }
}
