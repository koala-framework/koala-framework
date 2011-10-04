<?php
class Vpc_Export_Json_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        // TODO: viewcache nicht deaktiveren
        // löschen muss korrekt eingebaut werden
        $ret['viewCache'] = false;

        $ret['contentSender'] = 'Vpc_Export_Json_ContentSender';
        return $ret;
    }
}
