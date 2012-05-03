<?php
class Kwc_Mail_Image_Mail_Component extends Kwc_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content']['component'] = 'Kwc_Mail_Image_Content_Component';
        $ret['ownModel'] = 'Kwc_Mail_Image_Mail_Model';
        return $ret;
    }
}
