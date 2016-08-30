<?php
class Kwc_Cards_Composite_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['cards'] = 'Kwc_Cards_TestComponent';
        return $ret;
    }
}
