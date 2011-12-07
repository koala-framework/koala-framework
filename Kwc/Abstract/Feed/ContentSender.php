<?php
class Kwc_Abstract_Feed_ContentSender extends Kwf_Component_Abstract_ContentSender_Abstract
{
    public function sendContent($includeMaster)
    {
        $xml = $this->_data->getComponent()->getXml();
        header('Content-type: application/rss+xml; charset: utf-8');
        echo $xml;
    }
}
