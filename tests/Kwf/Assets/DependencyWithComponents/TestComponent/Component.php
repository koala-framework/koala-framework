<?php
class Kwf_Assets_DependencyWithComponents_TestComponent_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['assets']['dep'][] = 'Test';
        return $ret;
    }
}
