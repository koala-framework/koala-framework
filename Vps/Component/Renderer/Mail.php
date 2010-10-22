<?php
class Vps_Component_Renderer_Mail extends Vps_Component_Renderer_Abstract
{
    const RENDER_HTML = 'html';
    const RENDER_TXT = 'txt';

    private $_type = self::RENDER_HTML;
    private $_recipient;
    private $_attachImages = false;

    public function setRenderFormat($renderFormat)
    {
        $this->_renderFormat = $renderFormat;
    }

    public function setRecipient(Vpc_Mail_Recipient_Interface $recipient = null)
    {
        $this->_recipient = $recipient;
    }

    public function setAttachImages($attachImages)
    {
        $this->_attachImages = $attachImages;
    }

    // $this->component() im Template rendert bei Mail mail.tpl
    protected function _formatRenderInfo($type, $config)
    {
        if ($type == 'component') {
            $type = 'mail';
            $config = array(
                'type' => $this->_type,
                'recipient' => $this->_recipient
            );
        }
    }

    protected function _getView()
    {
        $ret = new Vps_Component_View_Mail();
        $ret->setAttachImages($this->_attachImages);
        $ret->setRecipient($this->_recipient);
        $ret->setRenderFormat($this->_renderFormat);
        return $ret;
    }
}
