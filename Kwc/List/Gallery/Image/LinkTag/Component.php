<?php
class Kwc_List_Gallery_Image_LinkTag_Component extends Kwc_TextImage_ImageEnlarge_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_List_Gallery_Image_LinkTag_Model';
        return $ret;
    }
}
