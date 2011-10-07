<?php
abstract class Vpc_Basic_LinkTag_ComponentClass_Component extends Vpc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dataClass'] = 'Vpc_Basic_LinkTag_ComponentClass_Data';
        $ret['componentName'] = trlVps('Link.to Component');
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        $ret['targetComponentClass'] = null;
        return $ret;
    }

}
