<?php
class Kwf_Component_Cache_CacheDisabled_Test_Component extends Kwc_Abstract_Composite_Component
{
    public static $test = 'foo';

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['test'] = self::$test;
        return $ret;
    }
}
