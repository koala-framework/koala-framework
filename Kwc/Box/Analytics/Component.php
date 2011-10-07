<?php
class Vpc_Box_Analytics_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    protected function _getAnalyticsCode()
    {
        return Vps_Registry::get('config')->statistic->analyticsCode;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['analyticsCode'] = $this->_getAnalyticsCode();
        if ($ret['analyticsCode'] && !is_string($ret['analyticsCode'])) {
            throw new Vps_Exception("AnalyticsCode must be a string, '".gettype($ret['analyticsCode'])."' given");
        }
        return $ret;
    }
}
