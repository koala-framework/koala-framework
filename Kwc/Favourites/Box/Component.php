<?php
class Kwc_Favourites_Box_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'kwf/Kwc/Favourites/Box/Component.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['favourite'] = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass('Kwc_Favourites_Page_Component');
        if (!$ret['favourite']) {
            throw new Kwf_Exception('Could not find "Kwc_Favourites_Page_Component"');
        }
        return $ret;
    }
}
