<?php
class Vpc_Basic_LinkTag_FirstChildPage_Component extends Vpc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Link.First Child Page'),
            'componentIcon' => new Vps_Asset('page_go'),
            'dataClass' => 'Vpc_Basic_LinkTag_FirstChildPage_Data'
        ));
        return $ret;
    }
}
