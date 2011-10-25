<?php
class Kwc_FulltextSearch_Box_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['searchPage'] = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass('Kwc_FulltextSearch_Search_Component',
                                   array('subroot'=>$this->getData()));
        return $ret;
    }
}
