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
        return $this->getData()->getBaseProperty('statistics.analyticsCode');
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['analyticsCode'] = $this->_getAnalyticsCode();
        if ($ret['analyticsCode'] && !is_string($ret['analyticsCode'])) {
            throw new Kwf_Exception("AnalyticsCode must be a string, '".gettype($ret['analyticsCode'])."' given");
        }
        return $ret;
    }
}
