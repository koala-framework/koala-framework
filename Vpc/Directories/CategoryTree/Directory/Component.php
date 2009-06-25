<?php
class Vpc_Directories_CategoryTree_Directory_Component extends Vpc_Directories_Category_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_Directories_CategoryTree_Directory_Model';
        $ret['generators']['detail']['class'] = 'Vpc_Directories_CategoryTree_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Vpc_Directories_CategoryTree_Detail_Component';
        $ret['generators']['child']['component']['view'] = 'Vpc_Directories_CategoryTree_View_Component';

        $ret['categoryToItemModelName'] = null;

        return $ret;
    }

    public function getSelect()
    {
        $select = parent::getSelect();
        $select->whereNull('parent_id');
        return $select;
    }
}
