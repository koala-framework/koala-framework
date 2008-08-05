<?php
class Vpc_Directories_CategoryTree_Directory_Component extends Vpc_Directories_Category_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['tablename'] = 'Vpc_Directories_CategoryTree_Directory_Model';
        $ret['generators']['detail']['class'] = 'Vpc_Directories_CategoryTree_Directory_Generator';
        $ret['generators']['detail']['component'] = 'Vpc_Directories_CategoryTree_Detail_Component';

        $ret['categoryToItemTableName'] = null;

        $ret['order'] = 'pos ASC';

        return $ret;
    }

    public function getSelect()
    {
        $select = parent::getSelect();
        $select->where('parent_id IS NULL');
        return $select;
    }
}
