<?php
class Kwf_Assets_DependencyWithComponents_TestComponent_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['dep'][] = 'Test';
        return $ret;
    }
}
