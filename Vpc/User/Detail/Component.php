<?php
class Vpc_User_Detail_Component extends Vpc_Directories_Item_Detail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['general'] = 'Vpc_User_Detail_General_Component';
        return $ret;
    }
    
    public function getTemplateVars()
    {
        $table = Zend_Registry::get('userModel');
        $user = $table->getAuthedUser();
        if (!$user) { Vps_Setup::output404(); }
        
        $ret = parent::getTemplateVars();
        $generators = $this->_getSetting('generators');
        foreach ($generators['child']['component'] as $generator => $class) {
            $ret['items'][$generator] = Vpc_Abstract::getSetting($class, 'componentName');
        }
        return $ret;
    }
}
