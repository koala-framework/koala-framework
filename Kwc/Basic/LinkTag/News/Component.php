<?php
class Kwc_Basic_LinkTag_News_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['dataClass'] = 'Kwc_Basic_LinkTag_News_Data';
        $ret['componentName'] = trlKwfStatic('Link.to News');
        $ret['ownModel'] = 'Kwc_Basic_LinkTag_News_Model';
        $ret['assetsAdmin']['dep'][] = 'KwfFormFilterField';
        return $ret;
    }
}
