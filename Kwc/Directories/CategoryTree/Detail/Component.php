<?php
class Kwc_Directories_CategoryTree_Detail_Component extends Kwc_Directories_Category_Detail_Component
{
    static public function getSettings()
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['categoryList'] =
            'Kwc_Directories_CategoryTree_Detail_CategoryList_Component';
        $ret['generators']['child']['component']['breadcrumbs'] =
            'Kwc_Directories_CategoryTree_Detail_Breadcrumbs_Component';
        return $ret;
    }
}
