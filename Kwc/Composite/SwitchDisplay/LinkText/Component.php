<?php
class Vpc_Composite_SwitchDisplay_LinkText_Component extends Vpc_Basic_Textfield_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Link text');
        return $ret;
    }
}
