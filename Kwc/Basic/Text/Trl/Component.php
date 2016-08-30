<?php
class Kwc_Basic_Text_Trl_Component extends Kwc_Chained_Trl_MasterAsChild_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Basic/Text/Trl/CopyButton.js';
        $ret['editComponents'] = array();
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        return $ret;
    }
}
