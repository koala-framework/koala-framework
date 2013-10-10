<?php
class Kwc_Export_Xml_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        // TODO: viewcache nicht deaktiveren
        // löschen muss korrekt eingebaut werden
        $ret['viewCache'] = false;

        $ret['contentSender'] = 'Kwc_Export_Xml_ContentSender';
        return $ret;
    }

    public function getXml()
    {
        $exportData = $this->_data->parent->getComponent()->getExportData();

        $rootNode = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?>'."\n<data />");
        $this->_createXmlNode($exportData, $rootNode);
        return $rootNode->asXML();
    }

    protected function _createXmlNode($array, $appendTo)
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
