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

    public function getPartialCacheVars($nr)
    {
        return array_merge(parent::getPartialCacheVars($nr), $this->_doClearCache());
    }

    public function getCacheVars()
    {
        return array_merge(parent::getCacheVars(), $this->_doClearCache());
    }

    private function _doClearCache()
    {
        $c = $this->getData()->parent->getComponent()->getItemDirectory()->getComponent();
        $modelName = Vpc_Abstract::getSetting(get_class($c), 'categoryToItemTableName');

        $itemRef = Vpc_Directories_Category_Detail_List_Component::getTableReferenceData(
            $modelName, 'Item'
        );
        $ret = array();
        $ret[] = array(
            'model' => $modelName,
            'id' => $this->getData()->parent->getComponent()->getItemDetail()->getRow()->{$itemRef['refItemColumn']},
            'field' => $itemRef['itemColumn']
        );
        return $ret;
    }
}
