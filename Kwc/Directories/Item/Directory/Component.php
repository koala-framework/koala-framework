<?php
abstract class Kwc_Directories_Item_Directory_Component extends Kwc_Directories_Item_DirectoryNoAdmin_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assetsAdmin']['dep'][] = 'KwfAutoGrid';
        $ret['assetsAdmin']['dep'][] = 'KwfAutoForm';
        $ret['assetsAdmin']['dep'][] = 'KwfProxyPanel';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Directories/Item/Directory/Panel.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Directories/Item/Directory/TabsPanel.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Directories/Item/Directory/EditFormPanel.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Directories/Item/Directory/Plugin.js';
        $ret['extConfig'] = 'Kwc_Directories_Item_Directory_ExtConfigEditButtons';
        $ret['extConfigControllerIndex'] = 'Kwf_Component_Abstract_ExtConfig_None';
        return $ret;
    }
}
