<?php
abstract class Kwc_Basic_LinkTag_ComponentClass_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['dataClass'] = 'Kwc_Basic_LinkTag_ComponentClass_Data';
        $ret['componentName'] = trlKwfStatic('Link.to Component');
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['targetComponentClass'] = null;
        return $ret;
    }

}
