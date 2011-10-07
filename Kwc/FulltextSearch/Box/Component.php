<?php
class Vpc_FulltextSearch_Box_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['searchPage'] = Vps_Component_Data_Root::getInstance()
            ->getComponentByClass('Vpc_FulltextSearch_Search_Component',
                                   array('subroot'=>$this->getData()));
        return $ret;
    }
}
