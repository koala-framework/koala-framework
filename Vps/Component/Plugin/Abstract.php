<?php
abstract class Vps_Component_Plugin_Abstract extends Vps_Component_Abstract
{
    public static function getStaticCacheVars()
    {
        return array();
    }

    protected function _getSetting($setting)
    {
        return self::getSetting(get_class($this), $setting);
    }

    protected function _hasSetting($setting)
    {
        return self::hasSetting(get_class($this), $setting);
    }
}
