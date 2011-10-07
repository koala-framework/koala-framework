<?php
class Kwf_Component_Renderer_Mail extends Kwf_Component_Renderer_Abstract implements Kwf_View_MailInterface
{
    const RENDER_HTML = 'html';
    const RENDER_TXT = 'txt';

    private $_renderFormat = self::RENDER_HTML;
    private $_attachImages = false;
    private $_recipient;
    private $_images = array();

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
        $this->getRenderComponent()->getComponent()->addImage($image);
    }

    public function getRenderFormat()
    {
        return $this->_renderFormat;
    }

    public function setRenderFormat($renderFormat)
    {
        $this->_renderFormat = $renderFormat;
    }

    public function getRecipient()
    {
        return $this->_recipient;
    }

    public function setRecipient(Kwc_Mail_Recipient_Interface $recipient = null)
    {
        $this->_recipient = $recipient;
    }
}
