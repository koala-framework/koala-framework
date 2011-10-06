<?php
class Vps_Component_ComponentLinkModifiers_Page_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasComponentLinkModifiers'] = true;
        return $ret;
    }

    public function getComponentLinkModifiers()
    {
        return array(
            array(
                'type' => 'appendText',
                'text' => 'foobar'
            )
        );
    }
}
