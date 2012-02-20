<?php
abstract class Kwf_Component_Plugin_Abstract extends Kwf_Component_Abstract
{
    protected function _getSetting($setting)
    {
        return self::getSetting(get_class($this), $setting);
    }

    protected function _hasSetting($setting)
    {
        return self::hasSetting(get_class($this), $setting);
    }

    public function processOutput($output)
    {
        return $output;
    }
}
