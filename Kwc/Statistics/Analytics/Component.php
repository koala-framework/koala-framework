<?php
class Kwc_Statistics_Analytics_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasFooterIncludeCode'] = true;
        $ret['plugins'] = array('Kwc_Statistics_CookieBeforePlugin', 'Kwc_Statistics_CookieAfterPlugin');
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }

    protected function _getAnalyticsCode()
    {
        return $this->getData()->getBaseProperty('statistics.analytics.code');
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['code'] = $this->_getAnalyticsCode();
        if ($ret['code'] && !is_string($ret['code'])) {
            throw new Kwf_Exception("AnalyticsCode must be a string, '".gettype($ret['code'])."' given");
        }
        $ret['ignoreCode'] = false;
        if ($this->getData()->getBaseProperty('statistics.ignore') ||
            $this->getData()->getBaseProperty('statistics.analytics.ignore')
        ) {
            $ret['ignoreCode'] = true;
        }
        return $ret;
    }
}
