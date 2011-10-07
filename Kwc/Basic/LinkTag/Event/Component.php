<?php
class Vpc_Basic_LinkTag_Event_Component extends Vpc_Basic_LinkTag_News_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dataClass'] = 'Vpc_Basic_LinkTag_Event_Data';
        $ret['componentName'] = trlVps('Link.to Event');
        $ret['ownModel'] = 'Vpc_Basic_LinkTag_Event_Model';
        return $ret;
    }
}
