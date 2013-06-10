<?php
class CatchBox_Blog_Detail_Component extends Kwc_Blog_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['backLink'] = false;
        return $ret;
    }
}
