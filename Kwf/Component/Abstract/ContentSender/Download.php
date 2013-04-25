<?php
abstract class Kwf_Component_Abstract_ContentSender_Download extends Kwf_Component_Abstract_ContentSender_Abstract
{
    public function sendContent($includeMaster)
    {
        if ($this->checkAllowed()) {
            $this->sendDownload();
        }
    }

    abstract public function sendDownload();

    protected function checkAllowed()
    {
        $valid = Kwf_Media_Output_Component::isValid($this->_data->componentId);
        if ($valid == Kwf_Media_Output_IsValidInterface::ACCESS_DENIED) { // send non pdf content to show login-plugin
            $contentSender = new Kwf_Component_Abstract_ContentSender_Default($this->_data);
            $contentSender->sendContent(true);
            return false;
        } else if ($valid == Kwf_Media_Output_IsValidInterface::INVALID) {
            throw new Kwf_Exception_NotFound();
        }
        return $valid;
    }
}
