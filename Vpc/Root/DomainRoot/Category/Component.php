<?php
class Vpc_Root_DomainRoot_Category_Component extends Vpc_Root_Category_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['class'] = 'Vpc_Root_DomainRoot_Category_PageGenerator';
        return $ret;
    }
}
