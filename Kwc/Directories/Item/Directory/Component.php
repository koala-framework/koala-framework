<?php
abstract class Kwc_Directories_Item_Directory_Component extends Kwc_Directories_Item_DirectoryNoAdmin_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['assetsAdmin']['dep'][] = 'KwfAutoGrid';
        $ret['assetsAdmin']['dep'][] = 'KwfAutoForm';
        $ret['assetsAdmin']['dep'][] = 'KwfProxyPanel';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Directories/Item/Directory/Panel.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Directories/Item/Directory/TabsPanel.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Directories/Item/Directory/EditFormPanel.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Directories/Item/Directory/Plugin.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Directories/Item/Directory/GridPanel.js';
        $ret['extConfig'] = 'Kwc_Directories_Item_Directory_ExtConfigEditButtons';
        $ret['extConfigControllerIndex'] = 'Kwf_Component_Abstract_ExtConfig_None';
        $ret['multiFileUpload'] = false;
        return $ret;
    }
}
