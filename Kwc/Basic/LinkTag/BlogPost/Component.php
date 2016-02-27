<?php
class Kwc_Basic_LinkTag_BlogPost_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dataClass'] = 'Kwc_Basic_LinkTag_BlogPost_Data';
        $ret['componentName'] = trlKwfStatic('Link.to Blog Post');
        $ret['ownModel'] = 'Kwc_Basic_LinkTag_BlogPost_Model';
        return $ret;
    }
}
