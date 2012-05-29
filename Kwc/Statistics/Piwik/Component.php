<?php
class Kwc_Statistics_Piwik_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'kwf/Kwc/Statistics/Piwik/Component.js';
        $ret['assets']['dep'][] = 'KwfStatistics';
        $ret['enableLinkTracking'] = true;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['domain'] = $this->_getDomain();
        $ret['id'] = $this->_getIdSite();
        $ret['customVariables'] = $this->_getCustomVariables();
        $ret['enableLinkTracking'] = $this->_getSetting('enableLinkTracking');
        return $ret;
    }

    public function hasContent()
    {
        return !!$this->_getIdSite();
    }

    protected function _getDomain()
    {
        $cfg = Kwf_Config::getValueArray('statistic');
        return isset($cfg['piwikDomain']) ? $cfg['piwikDomain'] : null;
    }

    protected function _getIdSite()
    {
        $cfg = Kwf_Config::getValueArray('statistic');

        if (isset($cfg['ignore']) && $cfg['ignore']) {
            return null;
        }

        return isset($cfg['piwikId']) ? $cfg['piwikId'] : null;
    }

    protected function _getCustomVariables()
    {
        return array();
    }
}
