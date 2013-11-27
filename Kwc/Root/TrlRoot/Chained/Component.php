<?php
class Kwc_Root_TrlRoot_Chained_Component extends Kwc_Chained_Start_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings();
        $copySettings = array('editComponents', 'masterTemplate', 'resetMaster');
        $copyFlags = array('subroot');
        $ret = Kwc_Chained_Abstract_Component::getChainedSettings($ret, $masterComponentClass, 'Trl', $copySettings, $copyFlags);
        $ret['flags']['hasHome'] = true;
        $ret['flags']['chainedType'] = 'Trl';
        $ret['flags']['hasBaseProperties'] = true;
        $ret['baseProperties'] = array('language');
        return $ret;
    }

    public function getBaseProperty($propertyName)
    {
        if ($propertyName == 'language') {
            return $this->getData()->language;
        }
        return null;
    }
}
