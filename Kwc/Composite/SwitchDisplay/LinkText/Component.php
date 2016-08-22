<?php
class Kwc_Composite_SwitchDisplay_LinkText_Component extends Kwc_Basic_Textfield_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Link text');
        return $ret;
    }
}
