<?php
class Vpc_Directories_CategoryTree_Detail_Component extends Vpc_Directories_Category_Detail_Component
{
    static public function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['categoryList'] =
            'Vpc_Directories_CategoryTree_Detail_CategoryList_Component';
        $ret['generators']['child']['component']['breadcrumbs'] =
            'Vpc_Directories_CategoryTree_Detail_Breadcrumbs_Component';
        return $ret;
    }
}
