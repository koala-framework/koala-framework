<?php
class Kwc_Directories_Item_Directory_Cc_Component extends Kwc_Directories_List_Cc_Component
{
    public static function getCacheMetaForView($view)
    {
        // Das Generator-Child-Model von der View muss nicht mit rein, weil
        // die View ja 1:1 übernommen wird und dadurch vom master-CacheMeta
        // abgedeckt wird

        $dir = $view->parent->getComponent()->getItemDirectory();
        $dirClass = $dir;
        if ($dir instanceof Kwf_Component_Data) $dirClass = $dir->componentClass;

        $ret = array();
        if (Kwc_Abstract::hasSetting($dirClass, 'childModel')) {
            $model = Kwf_Model_Abstract::getInstance(
                Kwc_Abstract::getSetting($dirClass, 'childModel')
            );
            $ret[] = new Kwc_Directories_Item_Directory_Cc_CacheMeta($model);
        }
        return $ret;
    }
}
