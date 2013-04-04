<?php
class Kwc_FormCache_Form_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['useAjaxRequest'] = false;
        $ret['componentName'] = 'Contact';
        $ret['placeholder']['submitButton'] = 'Send';
        return $ret;
    }
}
