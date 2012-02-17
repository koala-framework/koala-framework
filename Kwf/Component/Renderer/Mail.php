<?php
class Kwf_Component_Renderer_Mail extends Kwf_Component_Renderer_Abstract implements Kwf_View_MailInterface
{
    const RENDER_HTML = 'html';
    const RENDER_TXT = 'txt';

    private $_renderFormat = self::RENDER_HTML;
    private $_recipient;

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

    protected function _getCacheName()
    {
        return 'mail_' . $this->_renderFormat;
    }

    public function getTemplate(Kwf_Component_Data $component, $type)
    {
        if ($type == 'Component') {
            $mailType = 'Mail.' . $this->getRenderFormat();
        } else if ($file == 'Partial') {
            $mailType = 'Partial.' . $this->getRenderFormat();
        }
        $template = Kwc_Abstract::getTemplateFile($component->componentClass, $mailType);
        if (!$template) {
            $template = parent::getTemplate($component, $type);
        }
        return $template;
    }
}
