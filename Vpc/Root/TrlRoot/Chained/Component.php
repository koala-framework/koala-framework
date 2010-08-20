<?php
class Vpc_Root_TrlRoot_Chained_Component extends Vpc_Abstract
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings();
        $ret = Vpc_Chained_Abstract_Component::getChainedSettings($ret, $masterComponentClass, 'Trl', array(), array('editComponents'));
        $ret['flags']['showInPageTreeAdmin'] = true;
        $ret['flags']['hasHome'] = true;
        $ret['flags']['hasLanguage'] = true;
        $ret['chainedType'] = 'Trl';
        return $ret;
    }

    public function getLanguage()
    {
        return $this->getData()->language;
    }
}
