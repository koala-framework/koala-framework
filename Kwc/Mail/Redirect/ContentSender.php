<?php
class Kwc_Mail_Redirect_ContentSender extends Kwf_Component_Abstract_ContentSender_Default
{
    public function sendContent($includeMaster)
    {
        $process = $this->_getProcessInputComponents($includeMaster);
        self::_callProcessInput($process);

        $r = $this->_data->getComponent()->getRedirectRow();

        // if it is of type redirect, do the redirect
        if ($r->type == 'redirect') {
            header('Location: '.$r->value);
        } else if ($r->type == 'showcomponent') {
            $c = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($r->value);
            $cs = Kwc_Abstract::getSetting($c->componentClass, 'contentSender');
            $cs = new $cs($c);
            $cs->sendContent($includeMaster);
        }

        self::_callPostProcessInput($process);
    }
}
