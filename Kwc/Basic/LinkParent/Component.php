<?php
class Vpc_Basic_LinkParent_Component extends Vpc_Basic_Link_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Link to parent page');
        $ret['generators']['child']['component']['linkTag'] =
            'Vpc_Basic_LinkTag_ParentPage_Component';
        $ret['ownModel'] = 'Vpc_Basic_LinkParent_Model';
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }
}
