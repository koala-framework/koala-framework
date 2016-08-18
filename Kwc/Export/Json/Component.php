<?php
class Kwc_Export_Json_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        // TODO: viewcache nicht deaktiveren
        // löschen muss korrekt eingebaut werden
        $ret['viewCache'] = false;

        $ret['contentSender'] = 'Kwc_Export_Json_ContentSender';
        return $ret;
    }
}
