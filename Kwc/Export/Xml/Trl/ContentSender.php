<?php
class Vpc_Export_Xml_Trl_ContentSender extends Vps_Component_Abstract_ContentSender_Default
{
    public function sendContent()
    {
        $data = $this->_data->getChildComponent('-child');
        $contentSender = Vpc_Abstract::getSetting($data->componentClass, 'contentSender');
        $contentSender = new $contentSender($data);
        $contentSender->sendContent();
    }
}
