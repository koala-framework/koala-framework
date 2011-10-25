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
}
