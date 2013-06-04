<?php
class TestTheme_Box_HeaderTitle_Component extends Kwc_Basic_Headlines_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        if (!$ret['headline1']) $ret['headline1'] = 'Lorem Ipsum';
        if (!$ret['headline2']) $ret['headline2'] = 'Dolor Sit Amet';
        return $ret;
    }
}
