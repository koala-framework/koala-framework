<?php
class Vpc_Abstract_Feed_ContentSender extends Vps_Component_Abstract_ContentSender_Default
{
    public function sendContent()
    {
        $xml = $this->_data->getComponent()->getXml();
        header('Content-type: application/rss+xml; charset: utf-8');
        echo $xml;
    }
}
