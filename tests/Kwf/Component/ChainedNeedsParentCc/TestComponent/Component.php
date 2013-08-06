<?php
class Kwf_Component_ChainedNeedsParentCc_TestComponent_Component extends Kwc_Abstract
{
    public static $needsParentComponentClass = true;
    public static function getSettings($parentComponentClass)
    {
        $ret = parent::getSettings();
        $ret['parentComponentClass'] = $parentComponentClass;
        return $ret;
    }
}
