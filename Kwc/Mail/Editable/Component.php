<?php
class Vpc_Mail_Editable_Component extends Vpc_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasResources'] = true;
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Mail/Editable/Panel.js';
        return $ret;
    }

    // Wird hier verwendet: Vpc_Mail_Editable_ComponentsModel
    public function getNameForEdit()
    {
        return Vpc_Abstract::getSetting($this->getData()->componentClass, 'componentName');
    }
}
