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
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Directories/Item/Directory/Panel.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Directories/Item/Directory/TabsPanel.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Directories/Item/Directory/EditFormPanel.js';
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

    public static function getCacheMetaForView($view)
    {
        $ret = array();

        $dir = $view->parent->getComponent()->getItemDirectory();
        if (is_string($dir)) {
            $dirs = Kwf_Component_Data_Root::getInstance()->getComponentsByClass($dir);
        } else {
            $dirs = array($dir);
        }
        foreach ($dirs as $dir) {
            $generators = Kwf_Component_Generator_Abstract::getInstances($dir, array('generator'=>'detail'));
            if (!isset($generators[0])) {
                throw new Kwf_Exception("can't find detail generator"); //oder darf das auftreten?
                continue;
            }
            $generator = $generators[0];
            $pattern = null;
            if ($generator->getModel()->hasColumn('component_id')) {
                //wenns eine component_id gibt und die view unter dem directory liegt können wir genauer löschen
                $c = $view->parent;
                if ($c && $c->componentId == $dir->componentId) {
                    $pattern = '{component_id}-view';
                } else {
                    while ($c && $c->componentId != $dir->componentId) $c = $c->parent;
                    if ($c) {
                        $pattern = '{component_id}%-view'; // Falls Directory ein parent ist, kann man mit diesem Pattern löschen, sonst nicht
                    }
                }
            }
            $ret[] = new Kwf_Component_Cache_Meta_Static_Model($generator->getModel(), $pattern);
        }
        return $ret;
    }

}
