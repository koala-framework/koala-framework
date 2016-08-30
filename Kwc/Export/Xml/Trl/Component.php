<?php
class Kwc_Export_Xml_Trl_Component extends Kwc_Chained_Trl_MasterAsChild_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['contentSender'] = 'Kwc_Export_Xml_Trl_ContentSender';
        return $ret;
    }

    public function getExportData()
    {
        //eine ebene weiter raufspringen, wegen masterAsChild
        return $this->getData()->parent->getComponent()->getExportData();
    }
}
