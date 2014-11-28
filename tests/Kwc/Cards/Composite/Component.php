<?php
class Kwc_Cards_Composite_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['cards'] = 'Kwc_Cards_TestComponent';
        return $ret;
    }
}
