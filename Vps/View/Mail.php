<?php
class Vps_View_Mail extends Vps_View implements Vps_View_MailInterface
{
    private $_images = array();
    protected $_attachImages = true;

    public function addImage(Zend_Mime_Part $image)
    {
        $this->_images[] = $image;
    }

    public function getImages()
    {
        return $this->_images;
    }

    public function getAttachImages()
    {
        return $this->_attachImages;
    }

    public function setAttachImages($attachImages)
    {
        $this->_attachImages = $attachImages;
    }
}
