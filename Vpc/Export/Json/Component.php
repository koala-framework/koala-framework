<?php
class Vpc_Export_Json_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        // TODO: viewcache nicht deaktiveren
        // löschen muss korrekt eingebaut werden
        $ret['viewCache'] = false;
        return $ret;
    }

    public function sendContent()
    {
        header('Content-type: application/json; charset: utf-8');
        echo Zend_Json::encode($this->getData()->parent->getComponent()->getExportData());
    }
}
