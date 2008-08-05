<?php
class Vpc_Directories_CategoryTree_Detail_Component extends Vpc_Directories_Category_Detail_Component
{
    static public function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['categoryList'] =
            'Vpc_Directories_CategoryTree_Detail_CategoryList_Component';
        return $ret;
    }
}
