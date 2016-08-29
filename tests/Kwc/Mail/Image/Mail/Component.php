<?php
class Kwc_Mail_Image_Mail_Component extends Kwc_Mail_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['content']['component'] = 'Kwc_Mail_Image_Content_Component';
        $ret['ownModel'] = 'Kwc_Mail_Image_Mail_Model';
        $ret['docType'] = false;
        return $ret;
    }
}
