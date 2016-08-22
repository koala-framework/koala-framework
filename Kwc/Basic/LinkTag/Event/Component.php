<?php
class Kwc_Basic_LinkTag_Event_Component extends Kwc_Basic_LinkTag_News_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['dataClass'] = 'Kwc_Basic_LinkTag_Event_Data';
        $ret['componentName'] = trlKwfStatic('Link.to Event');
        $ret['ownModel'] = 'Kwc_Basic_LinkTag_Event_Model';
        return $ret;
    }
}
