<?php
class Kwc_Composite_SwitchDisplay_LinkText_Component extends Kwc_Basic_Textfield_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Link text');
        return $ret;
    }
}
