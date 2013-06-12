<?php
class CatchBox_Blog_Detail_Component extends Kwc_Blog_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['backLink'] = false;
        $ret['generators']['child']['component']['comments'] = 'CatchBox_Blog_Comments_Directory_Component';
        return $ret;
    }
}
