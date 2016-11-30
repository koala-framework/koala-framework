<?php
class Kwc_Mail_Redirect_ContentSender extends Kwf_Component_Abstract_ContentSender_Default
{
    public function sendContent($includeMaster)
    {
        $process = $this->_getProcessInputComponents($includeMaster);
        self::_callProcessInput($process);

        $r = $this->_data->getComponent()->getRedirectUrl();
        header('Location: '.$r);
        self::_callPostProcessInput($process);
    }
}
