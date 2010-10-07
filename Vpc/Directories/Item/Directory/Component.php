<?php
abstract class Vpc_Directories_Item_Directory_Component extends Vpc_Directories_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => 'Vpc_Directories_Item_Detail_Component'
        );
        $ret['generators']['child']['component']['view'] = 'Vpc_Directories_List_View_Component';
        $ret['useDirectorySelect'] = false;
        $ret['assetsAdmin']['dep'][] = 'VpsAutoGrid';
        $ret['assetsAdmin']['dep'][] = 'VpsAutoForm';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Directories/Item/Directory/Panel.js';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Directories/Item/Directory/TabsPanel.js';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Directories/Item/Directory/EditFormPanel.js';
        $ret['extConfig'] = 'Vpc_Directories_Item_Directory_ExtConfigEditButtons';
        return $ret;
    }

    protected function _getItemDirectory()
    {
        return $this->getData();
    }

    public function getCacheMetaForView($pattern)
    {
        $model = $this->getData()->getGenerator('detail')->getModel();
        return array(
            new Vps_Component_Cache_Meta_Static_Model($model, $pattern)
        );
    }
}
