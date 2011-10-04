<?php
class Vpc_Directories_Item_Directory_Cc_Component extends Vpc_Directories_List_Cc_Component
{
    public static function getCacheMetaForView($view)
    {
        // Das Generator-Child-Model von der View muss nicht mit rein, weil
        // die View ja 1:1 übernommen wird und dadurch vom master-CacheMeta
        // abgedeckt wird

        $dir = $view->parent->getComponent()->getItemDirectory();
        $dirClass = $dir;
        if ($dir instanceof Vps_Component_Data) $dirClass = $dir->componentClass;

        $ret = array();
        if (Vpc_Abstract::hasSetting($dirClass, 'childModel')) {
            $model = Vps_Model_Abstract::getInstance(
                Vpc_Abstract::getSetting($dirClass, 'childModel')
            );
            $ret[] = new Vpc_Directories_Item_Directory_Cc_CacheMeta($model);
        }
        return $ret;
    }
}
