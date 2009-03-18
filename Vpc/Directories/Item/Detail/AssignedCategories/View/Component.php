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

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        // TODO: Nur bei eigener ID löschen, dazu müsste man das Feld angeben können
        $c = $this->getData()->parent->getComponent()->getItemDirectory()->getComponent();
        $ret[] = array(
            'model' => Vpc_Abstract::getSetting(get_class($c), 'categoryToItemTableName'),
            'id' => null
        );
        return $ret;
    }
}
