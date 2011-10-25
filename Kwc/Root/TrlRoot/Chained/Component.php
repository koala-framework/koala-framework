<?php
class Kwc_Root_TrlRoot_Chained_Component extends Kwc_Abstract
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings();
        $copySettings = array('editComponents');
        $copyFlags = array('subroot');
        $ret = Kwc_Chained_Abstract_Component::getChainedSettings($ret, $masterComponentClass, 'Trl', $copySettings, $copyFlags);
        $ret['flags']['hasHome'] = true;
        $ret['flags']['hasLanguage'] = true;
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }

    public function getLanguage()
    {
        return $this->getData()->language;
    }
}
