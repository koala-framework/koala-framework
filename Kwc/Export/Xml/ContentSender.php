<?php
class Kwc_Export_Xml_ContentSender extends Kwf_Component_Abstract_ContentSender_Abstract
{
    public function sendContent($includeMaster)
    {
        header('Content-type: application/xml; charset: utf-8');
        echo $this->_data->getComponent()->getXml();
    }
}
