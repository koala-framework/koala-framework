<?php
class Vps_View_Mail extends Vps_View implements Vps_View_MailInterface
{
    private $_images = array();
    protected $_attachImages = true;
    protected $_masterTemplate = null;

    /*
    public function render($name)
    {
        if (!is_null($this->_masterTemplate)) {
            //TODO: partial von Zend_View verwenden
            $this->renderedTemplate = parent::render($name);
            $name = $this->getMasterTemplate();
        }
        return parent::render($name);
    }
    */

    public function setMasterTemplate($tpl)
    {
        $this->_masterTemplate = $tpl;
    }

    public function getMasterTemplate()
    {
        return $this->_masterTemplate;
    }

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
