<?php
class Kwc_Box_Analytics_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasFooterIncludeCode'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }

    protected function _getAnalyticsCode()
    {
        return Kwf_Registry::get('config')->statistic->analyticsCode;
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
