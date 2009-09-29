<?php
class Vpc_Mail_Image_Mail_Component extends Vpc_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content']['component'] = 'Vpc_Mail_Image_Content_Component';
        $ret['ownModel'] = 'Vpc_Mail_Image_Mail_Model';
        return $ret;
    }

    public function getImages()
    {
        return $this->_images;
    }
}
