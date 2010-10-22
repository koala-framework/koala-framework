<?php
class Vps_Component_View_Mail extends Vps_Component_View implements Vps_View_MailInterface
{
    private $_images = array();
    protected $_attachImages = true;
    private $_renderFormat = null;
    private $_recipient = null;

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

    public function addImage(Zend_Mime_Part $image)
    {
        $data = $this->data;
        while ($data && !$data->getComponent() instanceof Vpc_Mail_Component) {
            $data = $data->parent;
        }

        if ($data) {
            $data->getComponent()->addImage($image);
        }
    }

    public function getRenderFormat()
    {
        return $this->_renderFormat;
    }

    public function setRenderFormat($renderFormat)
    {
        $this->_renderFormat = $renderFormat;
    }

    public function setRecipient($recipient)
    {
        $this->_recipient = $recipient;
    }

    public function getRecipient()
    {
        return $this->_recipient;
    }
}
