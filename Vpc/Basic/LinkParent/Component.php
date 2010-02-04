<?php
class Vpc_Basic_LinkParent_Component extends Vpc_Basic_Link_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trl('Parent page link');
        $ret['generators']['child']['component']['linkTag'] =
            'Vpc_Basic_LinkTag_ParentPage_Component';
        $ret['ownModel'] = 'Vpc_Basic_LinkParent_Model';
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }
}
