<?php
class GreyBox_FulltextSearch_Box_Component extends Kwc_FulltextSearch_Box_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['hideSubmit'] = true;
        return $ret;
    }
}
