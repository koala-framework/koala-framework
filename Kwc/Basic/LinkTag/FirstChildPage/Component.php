<?php
class Kwc_Basic_LinkTag_FirstChildPage_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlKwf('Link.First Child Page'),
            'componentIcon' => new Kwf_Asset('page_go'),
            'dataClass' => 'Kwc_Basic_LinkTag_FirstChildPage_Data'
        ));
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        return $ret;
    }
}
