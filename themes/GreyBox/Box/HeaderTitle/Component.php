<?php
class GreyBox_Box_HeaderTitle_Component extends Kwc_Basic_Headlines_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Header Title');
        $ret['assets']['files'][] = 'kwf/themes/GreyBox/Box/HeaderTitle/Component.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        if (!$ret['headline1']) $ret['headline1'] = 'Lorem Ipsum';
        if (!$ret['headline2']) $ret['headline2'] = 'Dolor Sit Amet';
        return $ret;
    }
}
