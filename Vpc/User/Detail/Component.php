<?php
class Vpc_User_Detail_Component extends Vpc_Directories_Item_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['general'] = 'Vpc_User_Detail_GeneralCommunity_Component';
        return $ret;
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $generators = $this->_getSetting('generators');
        foreach ($generators['child']['component'] as $generator => $class) {
            $ret['items'][$generator] = Vpc_Abstract::getSetting($class, 'componentName');
        }
        return $ret;
    }
}
