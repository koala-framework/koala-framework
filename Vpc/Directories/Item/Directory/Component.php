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
        $ret = array();

        $dir = $view->parent->getComponent()->getItemDirectory();
        if (is_string($dir)) {
            $dirs = Vps_Component_Data_Root::getInstance()->getComponentsByClass($dir);
        } else {
            $dirs = array($dir);
        }
        foreach ($dirs as $dir) {
            $pattern = null;
            $c = $view->parent;
            if ($c && $c->componentId == $dir->componentId) {
                $pattern = '{component_id}-view';
            } else {
                while ($c && $c->componentId != $dir->componentId) $c = $c->parent;
                if ($c) $pattern = '{component_id}%-view'; // Falls Directory ein parent ist, kann man mit diesem Pattern löschen, sonst nicht
            }
            if ($pattern) { // dbId
                $generators = Vpc_Abstract::getSetting($c->componentClass, 'generators');
                if (isset($generators['detail']['dbIdShortcut'])) {
                    $pattern = str_replace('{component_id}', '%', $pattern);
                }
            }
            $generators = Vps_Component_Generator_Abstract::getInstances($dir, array('generator'=>'detail'));
            if (isset($generators[0])) {
                $ret[] = new Vps_Component_Cache_Meta_Static_Model($generators[0]->getModel(), $pattern);
            }
        }
        return $ret;
    }

}
