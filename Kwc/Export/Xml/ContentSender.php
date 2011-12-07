<?php
class Kwc_Export_Xml_ContentSender extends Kwf_Component_Abstract_ContentSender_Abstract
{
    public function sendContent()
    {
        header('Content-type: application/xml; charset: utf-8');
        echo $this->_getXml();
    }

    private function _getXml()
    {
        $exportData = $this->_data->parent->getComponent()->getExportData();

        $rootNode = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?>'."\n<data />");
        $this->_createXmlNode($exportData, $rootNode);
        return $rootNode->asXML();
    }

    private function _createXmlNode($array, $appendTo)
    {
        foreach ($array as $k => $v) {
            $tag = $k;
            if (is_numeric($tag)) $tag = 'item';

            if (is_array($v)) {
                $child = $appendTo->addChild($tag);
                $this->_createXmlNode($v, $child);
            } else {
                $appendTo->addChild($tag, $v);
            }
        }
    }
}
