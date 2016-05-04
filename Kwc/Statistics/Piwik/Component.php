<?php
class Kwc_Statistics_Piwik_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['disableCookies'] = false;
        $ret['enableLinkTracking'] = true;
        $ret['customTrackingDomain'] = false;
        $ret['flags']['hasFooterIncludeCode'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $config = array();
        $config['domain'] = $this->_getDomain();
        $config['siteId'] = $this->_getIdSite();
        $config['customVariables'] = $this->_getCustomVariables();
        $config['enableLinkTracking'] = $this->_getSetting('enableLinkTracking');
        $config['disableCookies'] = $this->_getSetting('disableCookies');
        $config['customTrackingDomain'] = $this->_getSetting('customTrackingDomain');
        $config['additionalConfiguration'] = $this->_getAdditionalConfiguration();
        $config['ignore'] =
            $this->getData()->getBaseProperty('statistics.ignore') ||
            $this->getData()->getBaseProperty('statistics.piwik.ignore');
        $ret['config'] = $config;
        return $ret;
    }

    public function hasContent()
    {
        return !!$this->_getIdSite();
    }

    protected function _getDomain()
    {
        return $this->getData()->getBaseProperty('statistics.piwik.domain');
    }

    protected function _getIdSite()
    {
        $ret = $this->getData()->getBaseProperty('statistics.piwik.id');
        return $ret;
    }

    protected function _getCustomVariables()
    {
        return array();
    }

    protected function _getAdditionalConfiguration()
    {
        return array();
    }
}
