<?php
class Vps_Component_Renderer_Mail extends Vps_Component_Renderer_Abstract
{
    const TYPE_HTML = 'html';
    const TYPE_TXT = 'txt';

    private $_type = self::TYPE_HTML;
    private $_recipient;
    private $_attachImages = false;

    public function setType($type = self::TYPE_HTML)
    {
        $this->_type = $type;
    }

    public function setRecipient(Vpc_Mail_Recipient_Interface $recipient = null)
    {
        $this->_recipient = $recipient;
    }

    protected function _executeOutputPlugin($plugin, $output)
    {
        return $plugin->processMailOutput($output, $this->_currentRecipient);
    }

    public function setAttachImages($attachImages)
    {
        $this->_attachImages = $attachImages;
    }

    protected function _getOutputConfig($type)
    {
        // Muss hier gemacht werden, da recipient in config
        $ret = array();
        if ($type == 'component') {
            $ret['type'] = 'mail';
            $ret['config'] = array($this->_type, $this->_recipient);
        }
        return $ret;
    }

    protected function _getView()
    {
        $ret = new Vps_Component_View_Mail();
        $ret->setAttachImages($this->_attachImages);
        return $ret;
    }
}
