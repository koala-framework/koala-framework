<?php
class Kwf_Component_Cache_ComponentLinkModifierCallback_LinkTargetPage_Component extends Kwc_Abstract
{
    public static $linkModifierContent = 'foo';

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
                'type' => 'callback',
                'callback' => array(get_class($this), 'modifyComponentLink')
            )
        );
    }

    public static function modifyComponentLink($ret, $componentId, $settings)
    {
        return $ret . self::$linkModifierContent;
    }
}
