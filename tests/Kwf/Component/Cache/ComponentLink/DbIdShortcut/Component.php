<?php
class Kwf_Component_Cache_ComponentLink_DbIdShortcut_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['childpages'] = array(
            'class' => 'Kwf_Component_Cache_ComponentLink_DbIdShortcut_Generator',
            'component' => 'Kwc_Basic_Empty_Component',
            'dbIdShortcut' => 'foo_'
        );
        $ret['childModel'] = 'Kwf_Component_Cache_ComponentLink_DbIdShortcut_Model';
        return $ret;
    }
}
