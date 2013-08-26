<?php
class GreyBox_Blog_Comments_View_Component extends Kwc_Posts_Directory_View_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }


    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['entriesCount'] = $this->getPagingCount();
        return $ret;
    }

}
