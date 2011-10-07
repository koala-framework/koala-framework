<?php
class Kwc_Mail_Image_Mail extends Kwc_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content']['component'] = 'Kwc_Mail_Image_Content_Component';
        $ret['ownModel'] = 'Kwc_Mail_Image_MailModel';
        return $ret;
    }
    
    public function getImages()
    {
        return $this->_images;
    }
}
