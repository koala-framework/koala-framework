<?php
class Vps_View_Mail extends Vps_View
{
    private $_images = array();

    public function addImage(Zend_Mime_Part $image)
    {
        $this->_images[] = $image;
    }

    public function getImages()
    {
        return $this->_images;
    }

    public static function getCid($image)
    {
        return md5($image);
    }

}
