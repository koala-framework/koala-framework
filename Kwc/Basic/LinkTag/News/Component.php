<?php
class Vpc_Basic_LinkTag_News_Component extends Vpc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dataClass'] = 'Vpc_Basic_LinkTag_News_Data';
        $ret['componentName'] = trlVps('Link.to News');
        $ret['ownModel'] = 'Vpc_Basic_LinkTag_News_Model';
        return $ret;
    }
}
