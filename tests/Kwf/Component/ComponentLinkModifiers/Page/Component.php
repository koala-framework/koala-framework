<?php
class Kwf_Component_ComponentLinkModifiers_Page_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
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
