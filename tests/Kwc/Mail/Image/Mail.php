<?php
class Vpc_Mail_Image_Mail extends Vpc_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content']['component'] = 'Vpc_Mail_Image_Content_Component';
        $ret['ownModel'] = 'Vpc_Mail_Image_MailModel';
        return $ret;
    }
    
    public function getImages()
    {
        return $this->_images;
    }
}
