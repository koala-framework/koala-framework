<?php
class Vpc_Directories_Item_Detail_AssignedCategories_View_Component
    extends Vpc_Directories_List_ViewPage_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['paging'] = false;
        return $ret;
    }

    public function getCacheMeta()
    {
        $ret = parent::getCacheMeta();
        /* TODO Cache: so funktionierts nicht (%_{$column} wird nicht mit {} reingeschrieben, ist nur langsam, findet aber nichts), also mal auskommentiert, wenn es mal auftritt, beheben
        $c = $this->getData()->parent->getComponent()->getItemDirectory()->getComponent();
        $modelName = Vpc_Abstract::getSetting(get_class($c), 'categoryToItemModelName');
        $itemRef = Vpc_Directories_Category_Detail_List_Component::getTableReferenceData(
            $modelName, 'Item'
        );
        $column = $itemRef['refItemColumn'];
        $ret[] = new Vps_Component_Cache_Meta_Static_Model($modelName, "%_{$column}");
        */
        return $ret;
    }
}
