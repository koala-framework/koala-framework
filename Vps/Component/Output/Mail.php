<?php
class Vps_Component_Output_Mail extends Vps_Component_Output_NoCache
{
    const TYPE_HTML = 'html';
    const TYPE_TXT = 'txt';

    private $_type = 'html';
    private $_recipient;

    public function setType($type = self::TYPE_HTML)
    {
        $this->_type = $type;
    }

    public function setRecipient(Vpc_Mail_Recipient_Interface $recipient = null)
    {
        $this->_recipient = $recipient;
    }

    protected function _renderContent($componentId, $componentClass, $masterTemplate, $useCache = false)
    {
        $output = new Vps_Component_Output_ComponentMail();
        $output->setIgnoreVisible($this->ignoreVisible());
        return $output->render($this->_getComponent($componentId), $this->_type, $this->_recipient);
    }
}
