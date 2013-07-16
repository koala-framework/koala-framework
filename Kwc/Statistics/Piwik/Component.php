<?php
class Kwc_Statistics_Piwik_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'kwf/Kwc/Statistics/Piwik/Component.js';
        $ret['assets']['dep'][] = 'KwfStatistics';
        $ret['disableCookies'] = false;
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
        $ret['disableCookies'] = $this->_getSetting('disableCookies');
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
        $ret = isset($cfg['piwikId']) ? $cfg['piwikId'] : null;

        $ignore = false;
        if (isset($cfg['ignore']) && $cfg['ignore']) {
            $ignore = true;
        }

        $domain = Kwc_Root_DomainRoot_Domain_Component::getDomainComponent($this->getData());
        if ($domain) {
            $domains = Kwf_Config::getValueArray('kwc.domains');
            $domain = $domains[$domain->id];
            if (isset($domain['piwikId'])) $ret = $domain['piwikId'];
            if (isset($domain['statistik']['ignore'])) $ignore = $domain['statistik']['ignore'];
        }

        if (!$ignore) {
            return $ret;
        }
    }

    protected function _getCustomVariables()
    {
        return array();
    }
}
