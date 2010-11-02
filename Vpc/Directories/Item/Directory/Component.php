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

    public static function getCacheMetaForView($view)
    {
        $dir = $view->parent->getComponent()->getItemDirectory();
        $dirClass = $dir;
        $pattern = null;
        if ($dir instanceof Vps_Component_Data) {
            $dirClass = $dir->componentClass;
            $c = $view->parent;
            if ($c && $c->componentId == $dir->componentId) {
                $pattern = '{component_id}-view';
            } else {
                while ($c && $c->componentId != $dir->componentId) $c = $c->parent;
                if ($c) $pattern = '{component_id}%-view'; // Falls Directory ein parent ist, kann man mit diesem Pattern löschen, sonst nicht
            }
        }

        $ret = array();

        $generators = Vps_Component_Generator_Abstract::getInstances($dir, array('generator'=>'detail'));
        if (count($generators) != 1) throw new Vps_Exception("can't get detail generator");

        $ret[] = new Vps_Component_Cache_Meta_Static_Model($generators[0]->getModel(), $pattern);
        return $ret;
    }
}
