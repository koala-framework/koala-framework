<?php
class Kwc_User_Detail_Component extends Kwc_Directories_Item_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['general'] = 'Kwc_User_Detail_GeneralCommunity_Component';
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }
    
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $generators = $this->_getSetting('generators');
        foreach ($generators['child']['component'] as $generator => $class) {
            if (is_instance_of($class, 'Kwc_User_Detail_Menu_Component')) {
                unset($ret['items'][$generator]);
            } else {
                $ret['items'][$generator] = Kwc_Abstract::getSetting($class, 'componentName');
            }
        }
        return $ret;
    }
}
