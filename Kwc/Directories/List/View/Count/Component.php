<?php
class Kwc_Directories_List_View_Count_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['count'] = count($this->getData()->parent
            ->getComponent()->getItemIds());
        return $ret;
    }
}
