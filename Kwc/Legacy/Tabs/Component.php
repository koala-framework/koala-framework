<?php
// Legacy for compatibility with old css classes
class Kwc_Legacy_Tabs_Component extends Kwc_Tabs_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Tabs Legacy');
        $ret['assetsDefer']['dep'][] = 'KwfTabs';
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        Kwc_Abstract_List_Component::validateSettings($settings, $componentClass);
    }

    public function getAnchors()
    {
        return array();
    }
}
