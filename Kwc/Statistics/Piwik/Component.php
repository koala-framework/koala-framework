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
        $ret['flags']['hasFooterIncludeCode'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
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
        return $this->getData()->getBaseProperty('statistics.piwik.domain');
    }

    protected function _getIdSite()
    {
        $ret = null;
        if (!$this->getData()->getBaseProperty('statistics.ignore') &&
            !$this->getData()->getBaseProperty('statistics.piwik.ignore')
        ) {
            $ret = $this->getData()->getBaseProperty('statistics.piwik.id');
        }
        return $ret;
    }

    protected function _getCustomVariables()
    {
        return array();
    }
}
