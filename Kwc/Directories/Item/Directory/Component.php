<?php
abstract class Kwc_Directories_Item_Directory_Component extends Kwc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'component' => 'Kwc_Directories_Item_Detail_Component'
        );
        $ret['generators']['child']['component']['view'] = 'Kwc_Directories_List_View_Component';
        $ret['useDirectorySelect'] = false;
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

    public static function getItemDirectoryClasses($directoryClass)
    {
        return array($directoryClass);
    }

    protected function _getItemDirectory()
    {
        return $this->getData();
    }
}
