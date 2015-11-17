<?php
class Kwc_Statistics_Piwik_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['dep'][] = 'KwfLegacyStatistics';
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
        $ret['domain'] = $this->_getDomain();
        $ret['id'] = $this->_getIdSite();
        $ret['customVariables'] = $this->_getCustomVariables();
        $ret['enableLinkTracking'] = $this->_getSetting('enableLinkTracking');
        $ret['disableCookies'] = $this->_getSetting('disableCookies');
        $ret['customTrackingDomain'] = $this->_getSetting('customTrackingDomain');
        $ret['additionalConfiguration'] = $this->_getAdditionalConfiguration();
        $ret['ignoreCode'] = false;
        if ($this->getData()->getBaseProperty('statistics.ignore') ||
            $this->getData()->getBaseProperty('statistics.piwik.ignore')
        ) {
            $ret['ignoreCode'] = true;
        }
        return $ret;
    }

    protected function _getAdditionalConfiguration()
    {
        return array();
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
}
