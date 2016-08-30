<?php
class Kwc_Directories_CategoryTree_Directory_Component extends Kwc_Directories_Category_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['childModel'] = 'Kwc_Directories_CategoryTree_Directory_Model';
        $ret['generators']['detail']['class'] = 'Kwc_Directories_CategoryTree_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Kwc_Directories_CategoryTree_Detail_Component';
        $ret['generators']['child']['component']['view'] = 'Kwc_Directories_CategoryTree_View_Component';

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
