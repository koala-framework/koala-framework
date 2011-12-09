<?php
class Kwc_Export_Xml_Trl_ContentSender extends Kwf_Component_Abstract_ContentSender_Abstract
{
    public function sendContent($includeMaster)
    {
        $data = $this->_data->getChildComponent('-child');
        $contentSender = Kwc_Abstract::getSetting($data->componentClass, 'contentSender');
        $contentSender = new $contentSender($data);
        $contentSender->sendContent($includeMaster);
    }
}
